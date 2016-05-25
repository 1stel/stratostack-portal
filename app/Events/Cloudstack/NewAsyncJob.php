<?php

namespace App\Events\Cloudstack;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewAsyncJob extends Event
{
    use SerializesModels;

    public $jobId;
    public $userId;
    public $userIpAddress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($jobId, $userId = null, $userIpAddress)
    {
        //
        $this->jobId = $jobId;
        $this->userId = $userId;
        $this->userIpAddress = $userIpAddress;
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
