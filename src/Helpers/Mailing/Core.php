<?php

namespace Eduard\Mailing\Helpers\Mailing;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use App\Events\SendEmailConfirmRestorePassword;
use App\Events\SendEmailRestorePassword;
use Eduard\Analitycs\Models\WebSiteCustomer;
use Eduard\Account\Helpers\SendMail;
use Eduard\Account\Helpers\SendMailMasive;
use Eduard\Account\Models\ContactClient;
use Eduard\Account\Models\NotificationsClient;
use Eduard\Account\Models\PasswordReset;
use Eduard\Account\Helpers\System\CoreHttp;
use Eduard\Account\Models\CustomersAccount;
use Eduard\Mailing\Events\SendMailIndex;
use Eduard\Mailing\Models\Mailing;
use Eduard\Mailing\Models\MailingCustomer;
use Eduard\Mailing\Models\MailingIndex;
use Eduard\Account\Helpers\Account\Customer;

class Core
{
    /**
     * @var CoreHttp
     */
    protected $coreHttp;

    /**
     * @var Customer
     */
    protected $customer;

    public function __construct(
        CoreHttp $coreHttp,
        Customer $customer
    ) {
        $this->coreHttp = $coreHttp;
        $this->customer = $customer;
    }

    /**
     * @inheritDoc
     */
    public function createMail($data, $client)
    {
        try {
            $dateProgram = date("Y-m-d H:i:s");

            if ($data["timeExecute"] == "program") {
                $dateProgram = $data["date_program"];
            }

            $newMailing = new Mailing();
            $newMailing->name = $data["name"];
            $newMailing->description = $data["description"];
            $newMailing->run_date = $dateProgram;
            $newMailing->send = 0;
            $newMailing->template = $data["mail_template"];
            $newMailing->preview_mail = $data['previewMail'];
            $newMailing->id_client = $client->id;
            $newMailing->created_at = date("Y-m-d H:i:s");
            $newMailing->updated_at = null;
            $newMailing->save();

            foreach ($data["selectedIndex"] as $index) {
                $this->createMailIndex($client->id, $index, $newMailing->id);
            }
        } catch (Exception $e) {
            return null;
        }
    }

    public function createMailIndex($idClient, $idIndex, $idMail)
    {
        try {
            $newMailingIndex = new MailingIndex();
            $newMailingIndex->send = 0;
            $newMailingIndex->id_client = $idClient;
            $newMailingIndex->id_index = $idIndex;
            $newMailingIndex->id_mail = $idMail;
            $newMailingIndex->created_at = date("Y-m-d H:i:s");
            $newMailingIndex->updated_at = null;
            $newMailingIndex->save();
            Event::dispatch(new SendMailIndex($idClient, $idIndex, $idMail, $newMailingIndex->id));
        } catch (Exception $e) {
            return null;
        }
    }

    public function getMailById($idMail)
    {
        return Mailing::find($idMail);
    }

    public function getMailIndexById($idMail)
    {
        return MailingIndex::find($idMail);
    }

    public function getCustomersByIndex($idClient, $idIndex)
    {
        return WebSiteCustomer::where("id_client", $idClient)->where("id_index", $idIndex)->get();
    }

    public function proccessMailingIndex($idClient, $idIndex, $idMail, $idMailingIndex)
    {
        $allCustomers = $this->getCustomersByIndex($idClient, $idIndex);
        $mail = $this->getMailById($idMail);
        $mailIndex = $this->getMailIndexById($idMailingIndex);
        $countMailSender = 0;

        if ($mail == null) {
            return;
        }

        foreach ($allCustomers as $customer) {
            $this->createMailingCustomer($idMailingIndex, $customer->id, true);
            $this->sendMailingCustomer($mail->name, $customer->email, $mail->template);
            $customer->send_mail = $customer->send_mail + 1;
            $customer->save();
            $countMailSender++;
        }

        $mailIndex->send = $mailIndex->send + $countMailSender;
        $mailIndex->updated_at = date("Y-m-d H:i:s");
        $mailIndex->save();
        $mail->send = $mail->send + $countMailSender;
        $mail->updated_at = date("Y-m-d H:i:s");
        $mail->save();
    }

    public function sendMailingCustomer($name, $to, $template)
    {
        new SendMailMasive($name, $to, $template);
    }

    public function createMailingCustomer($idMailingIndex, $idCustomer, $status)
    {
        try {
            $newMailingCustomer = new MailingCustomer();
            $newMailingCustomer->id_mailing_index = $idMailingIndex;
            $newMailingCustomer->id_website_customer = $idCustomer;
            $newMailingCustomer->sending = $status;
            $newMailingCustomer->created_at = date("Y-m-d H:i:s");
            $newMailingCustomer->updated_at = null;
            $newMailingCustomer->save();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAllCustomerMailing(array $body, array $header = [])
    {
        return $this->customer->executeWithValidation(
            function() use ($header, $body) {
                $this->customer->validateCustomerKey($header);

                if (!isset($body["mail-id"])) {
                    throw new Exception("Parametros no validos.");
                }

                $mail = $this->getMailById($body["mail-id"]);

                if ($mail == null) {
                    throw new Exception("El mail solicitado no existe.");
                }

                $allCustomers = [];

                foreach ($mail->allMailingIndex as $mailIndex) {
                    $listCustomer = [];

                    foreach ($mailIndex->allCustomers as $mailCustomer) {
                        $webSiteCustomer = $mailCustomer->customerWebSite;

                        $listCustomer[] = [
                            "sender" => $mailCustomer->sending,
                            "created_at" => $mailCustomer->created_at,
                            "customer" => [
                                "id" => $webSiteCustomer->id,
                                "name" => $webSiteCustomer->name,
                                "email" => $webSiteCustomer->email,
                                "phone_number" => $webSiteCustomer->phone_number
                            ]
                        ];
                    }

                    $allCustomers[] = [
                        "index_id" => $mailIndex->index->id,
                        "index" => $mailIndex->index->name,
                        "customers" => $listCustomer,
                        "total_customer" => count($listCustomer)
                    ];
                }

                return $allCustomers;
            },
            "Proceso ejecutado exitosamente."
        );
    }

    public function getMailQuery(array $body, array $header = [])
    {
        return $this->customer->executeWithValidation(
            function() use ($header, $body) {
                $this->customer->validateCustomerKey($header);

                if (!isset($body["mail-id"])) {
                    throw new Exception("Parametros no validos.");
                }

                $mail = $this->getMailById($body["mail-id"]);
                $countUsers = 0;

                if ($mail == null) {
                    throw new Exception("El mail solicitado no existe.");
                }

                return [
                    "id" => $mail->id,
                    "name" => $mail->name,
                    "description" => $mail->description,
                    "run_date" => $mail->run_date,
                    "template" => $mail->template,
                    "preview" => $mail->preview_mail,
                    "indexes" => $this->getAllIndexNameMailData($countUsers, $mail->allMailingIndex),
                    "total_index" => $mail->allMailingIndex()->count() ?? 0,
                    "total_users" => $countUsers,
                    "total_send" => $mail->send,
                    "created_at" => $mail->created_at,
                    "updated_at" => $mail->updated_at
                ];
            },
            "Proceso ejecutado exitosamente."
        );
    }

    public function getAllIndexNameMailData(&$countUsers, $listMailingIndex)
    {
        $data = [];

        foreach ($listMailingIndex as $mailingIndex) {
            $countUsers = $countUsers + ($mailingIndex->allCustomers()->where("sending", 1)->count() ?? 0);

            $data[] = [
                "index" => [
                    "id" => $mailingIndex->index->id,
                    "code" => $mailingIndex->index->code,
                    "name" => $mailingIndex->index->name
                ],
                "send" => $this->getCountMailingIndexSend($mailingIndex->index->id, $mailingIndex->id_mail),
                "customers" => $countUsers
            ];
        }

        return $data;
    }

    public function getCountMailingIndexSend($idIndex, $idMail)
    {
        return MailingIndex::where('id_index', $idIndex)->where('id_mail', $idMail)->sum('send') ?? 0;
    }

    public function getAllMailSender(array $body, array $header = [])
    {
        return $this->customer->executeWithValidation(
            function() use ($header, $body) {
                $this->customer->validateCustomerKey($header);
                $customer = $this->customer->getCustomerByEncryption($header["customer-key"][0]);
                $page = 1;

                if (isset($body["pagination"])) {
                    $page = $body["pagination"];
                }

                $mailings = $customer->client->allMailing()->orderBy('id', 'desc')->paginate(6, ['*'], 'page', $page);

                $dataMail = $mailings->map(function($mail) {
                    return [
                        "id" => $mail->id,
                        "name" => $mail->name,
                        "description" => $mail->description,
                        "run_date" => $mail->run_date,
                        "preview" => $mail->preview_mail,
                        "send" => $mail->send,
                        "indexes" => $this->getAllIndexNameMail($mail->allMailingIndex)
                    ];
                });

                return [
                    'data' => $dataMail,
                    'current_page' => $mailings->currentPage(),
                    'last_page' => $mailings->lastPage(),
                    'per_page' => $mailings->perPage(),
                    'total' => $mailings->total()
                ];
            },
            "Proceso ejecutado exitosamente."
        );
    }

    public function getAllIndexNameMail($listMailingIndex)
    {
        $names = [];

        foreach ($listMailingIndex as $mailingIndex) {
            $names[] = $mailingIndex->index->name;
        }

        return $names;
    }

    public function createMailMasive(array $body, array $header = [])
    {
        return $this->customer->executeWithValidation(
            function() use ($header, $body) {
                $this->customer->validateCustomerKey($header);
                $customer = $this->customer->getCustomerByEncryption($header["customer-key"][0]);
                $this->customer->validateBodyMail($body);
                $this->createMail($body, $customer->client);

                return [];
            },
            "Proceso ejecutado exitosamente."
        );
    }
}