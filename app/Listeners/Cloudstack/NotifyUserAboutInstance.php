<?php

namespace App\Listeners\Cloudstack;

use App\User;
use Mail;
use App\Events\Cloudstack\InstanceWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserAboutInstance implements ShouldQueue
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
     * @param  InstanceWasCreated  $event
     * @return void
     */
    public function handle(InstanceWasCreated $event)
    {
        //
        $emailParameters = ['name' => $event->instance->name,
                            'ip' => $event->instance->nic[0]->ipaddress,
                            'password' => (isset($event->instance->password)) ? $event->instance->password : ''];

        $instanceName = $event->instance->name;
        $user = User::where('email', $event->instance->account)->first();

        Mail::send('emails.newinstance', $emailParameters, function ($m) use ($user, $instanceName) {
            $m->from('support@stratostack.com', 'StratoSTACK');
            $m->to($user->email, $user->name)->subject('New Instance: ' . $instanceName);
        });
    }
}
