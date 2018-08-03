<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RegisteredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    //会員仮登録、認証メール発送
    const JOB_TYPE_1 = 1;
    //
    const JOB_TYPE_2 = 2;

    public $job_type;
    public $register_data;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($job_type, $register_data)
    {
        $this->job_type = $job_type;
        $this->register_data = $register_data;
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
