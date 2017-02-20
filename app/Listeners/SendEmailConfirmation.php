<?php

namespace App\Listeners;

use Mail;
use Config;
use App\Events\UserHasRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailConfirmation
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
     * @param  UserHasRegistered  $event
     * @return void
     */
    public function handle(UserHasRegistered $event)
    {
        //
        Mail::send('emails.emailConfirm', ['user' => $event->user], function ($m) use ($event) {
            $m->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
            $m->to($event->user->email, $event->user->name)->subject('Please confirm your email address');
        });
    }
}
