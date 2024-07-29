<?php

namespace Eduard\Mailing\Listeners;

use Eduard\Mailing\Events\SendMailIndex;
use Eduard\Account\Helpers\Account\Customer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AfterSendMailIndex implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public string $connection = 'database';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public string $queue = 'send_mail_index';

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Handle the event.
     *
     * @param SendMailIndex $event
     * @return void
     */
    public function handle(SendMailIndex $event)
    {
        $this->customer->proccessMailingIndex(
            $event->idClient,
            $event->idIndex,
            $event->idMail,
            $event->idMailingIndex
        );
    }
}