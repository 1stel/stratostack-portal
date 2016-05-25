<?php

namespace App\Listeners\Cloudstack;

use App\Events\Cloudstack\InstanceWasCreated;
use App\Events\Cloudstack\JobFinished;
use App\Events\Cloudstack\NewAsyncJob;
use Cloudstack\CloudStackClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CheckJobStatus implements ShouldQueue
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
     * @param  NewAsyncJob  $event
     * @return void
     */
    public function handle(NewAsyncJob $event, CloudStackClient $acs)
    {
        Log::debug('Checking job status!');
        //
        $loopComplete = 0;

        while ($loopComplete == 0)
        {
            $result = $acs->queryAsyncJobResult(['jobid' => $event->jobId]);

            if ($result->jobstatus == 1)
            {
                Log::debug('Job finished! We are inside CheckJobStatus.');
                $loopComplete = 1;
                event(new JobFinished($result, $event->userId, $event->userIpAddress));

                switch ($result->jobinstancetype)
                {
                    case 'VirtualMachine':
                        if ($result->cmd == 'org.apache.cloudstack.api.command.user.vm.DeployVMCmd')
                            event(new InstanceWasCreated($result->jobresult->virtualmachine));
                        break;
                }
            }

            sleep(5);
        }
    }
}
