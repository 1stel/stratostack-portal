<?php

namespace App\Listeners;

use App\SiteConfig;
use App\Events\UserWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCloudstackUser
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
     * @param  UserWasCreated  $event
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        // $event has user at $user.
        // Extract first and last name.  ACS wants them separate.
        if (str_word_count($event->user->name) > 1) {
            list ($fname, $lname) = explode(' ', $event->user->name, 2);
        } else {
            $fname = $event->user->name;
            $lname = ' ';
        }

        // Grab Site Configuration
        $cfg = SiteConfig::all()->makeKVArray();

        $acsData = ['accounttype' => '0',
                    'email'       => $event->user->email,
                    'firstname'   => $fname,
                    'lastname'    => $lname,
                    'password'    => $event->user->password,
                    'username'    => $event->user->email,
                    'domainid'    => $cfg['domainId'],
        ];

        $result = app('Cloudstack\CloudStackClient')->createAccount($acsData); // !!NEEDS REVISION!! Error checking

        $event->user->acs_id = $result->account->user[0]->id;
        $event->user->save();
    }
}
