<?php

namespace App\Events\Cloudstack;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InstanceWasCreated extends Event
{
    use SerializesModels;

    public $instance;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($instance)
    {
        //
        $this->instance = $instance;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
