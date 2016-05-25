<?php

namespace App\Events\Cloudstack;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class JobFinished extends Event
{
    use SerializesModels;

    public $jobResult;
    public $userId;
    public $userIpAddress;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($jobResult, $userId, $userIpAddress)
    {
        //
        $this->jobResult = $jobResult;
        $this->userId = $userId;
        $this->userIpAddress = $userIpAddress;
        Log::debug('JobFinished Event fired');
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
