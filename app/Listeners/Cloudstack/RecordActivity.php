<?php

namespace App\Listeners\Cloudstack;

use Log;
use Auth;
use Request;
use App\Activity;
use App\Events\Cloudstack\JobFinished;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  JobFinished  $event
     * @return void
     */
    public function handle(JobFinished $event)
    {
        // Job result is in $event->jobResult
        Log::debug('RecordActivity listener fired');

        Log::debug("Job Instance type is {$event->jobResult->jobinstancetype}");
        Log::debug("Command is {$event->jobResult->cmd}");
        Log::debug("VM Name is {$event->jobResult->jobresult->virtualmachine->name}");
        Log::debug("Auth is " . $event->userId);
        Log::debug("User IP is " . Request::server('REMOTE_ADDR'));

        try {
            $subject = $this->translateSubjectType($event->jobResult->jobinstancetype);
            $eventType = $this->translateEvent($event->jobResult->cmd);
        }
        catch (\Exception $e)
        {
            return;
        }


        Activity::create(['subject_type' => $subject,
                          'subject_id'   => 0,
                          'event'        => "$eventType - {$event->jobResult->jobresult->virtualmachine->name}",
                          'user_id'      => $event->userId,
                          'ip'           => $event->userIpAddress
        ]);
    }

    private function translateSubjectType($jobInstanceType) {
        switch ($jobInstanceType) {
            case "VirtualMachine":
                return 'Instance';
            default:
                Log::debug('Unhandled subject translation request: ' . $jobInstanceType);
                throw new \Exception('Unable to translate subject type.');
        }
    }

    private function translateEvent($cmd) {
        switch (last(explode('.', $cmd))) {
            case "StartVMCmd":
                return 'Started';
            case "StopVMCmd":
                return 'Stopped';
            case "DeployVMCmd":
                return 'Created';
            case "DestroyVMCmd":
                return 'Destroyed';
            case "RebootVMCmd":
                return 'Rebooted';
            default:
                Log::debug('Unhandled event translation request: ' . $cmd);
                throw new \Exception('Unable to translate event.');
        }

    }
}
