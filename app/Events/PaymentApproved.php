<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user instances.
     *
     * @var \App\Models\User
     */

    public $payer, $payee;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $payer, User $payee)
    {
        $this->payer = $payer;
        $this->payee = $payee;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
