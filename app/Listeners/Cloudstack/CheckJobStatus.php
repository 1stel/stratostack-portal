<?php

namespace App\Listeners\Cloudstack;

use App\Events\Cloudstack\InstanceWasCreated;
use App\Events\Cloudstack\JobFinished;
use App\Events\Cloudstack\NewAsyncJob;
use App\Mail\VMPasswordChanged;
use App\User;
use Cloudstack\CloudStackClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CheckJobStatus implements ShouldQueue
{
    private $acs;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CloudStackClient $acs)
    {
        $this->acs = $acs;
    }

    /**
     * Handle the event.
     *
     * @param  NewAsyncJob $event
     * @return void
     */
    public function handle(NewAsyncJob $event)
    {
        $loopComplete = 0;

        while ($loopComplete == 0) {
            $result = $this->acs->queryAsyncJobResult(['jobid' => $event->jobId]);

            if ($result->jobstatus == 1) {
                $loopComplete = 1;
                event(new JobFinished($result, $event->userId, $event->userIpAddress));

                switch ($result->jobinstancetype) {
                    case 'VirtualMachine':
                        if ($result->cmd == 'org.apache.cloudstack.api.command.user.vm.DeployVMCmd') {
                            event(new InstanceWasCreated($result->jobresult->virtualmachine));
                        }
                        if ($result->cmd == 'org.apache.cloudstack.api.command.user.vm.ResetVMPasswordCmd') {
                            Mail::to(User::find($event->userId)->email)
                                ->queue(new VMPasswordChanged($result->jobresult->virtualmachine));
                        }
                        break;
                }
            }

            sleep(5);
        }
    }
}
