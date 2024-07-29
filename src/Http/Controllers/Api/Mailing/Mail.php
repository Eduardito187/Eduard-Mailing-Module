<?php

namespace Eduard\Mailing\Http\Controllers\Api\Mailing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Eduard\Account\Helpers\Account\Customer;

class Mail extends Controller
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * Constructor Account Customer
     */
    public function __construct(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createMailMasive(Request $request)
    {
        return response()->json(
            $this->customer->createMailMasive(
                $request->all(),
                $request->header()
            )
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAllMailSender(Request $request)
    {
        return response()->json(
            $this->customer->getAllMailSender(
                $request->all(),
                $request->header()
            )
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getMailQuery(Request $request)
    {
        return response()->json(
            $this->customer->getMailQuery(
                $request->all(),
                $request->header()
            )
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAllCustomerMailing(Request $request)
    {
        return response()->json(
            $this->customer->getAllCustomerMailing(
                $request->all(),
                $request->header()
            )
        );
    }
}