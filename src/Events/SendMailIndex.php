<?php

namespace Eduard\Mailing\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMailIndex
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public $idClient;

    /**
     * @var int
     */
    public $idIndex;

    /**
     * @var int
     */
    public $idMail;

    /**
     * @var int
     */
    public $idMailingIndex;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($idClient, $idIndex, $idMail, $idMailingIndex)
    {
        $this->idClient = $idClient;
        $this->idIndex = $idIndex;
        $this->idMail = $idMail;
        $this->idMailingIndex = $idMailingIndex;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new PrivateChannel('channel-name');
        return [];
    }
}