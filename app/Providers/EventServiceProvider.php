<?php namespace App\Providers;

use App\Activity;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Request;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserHasRegistered'             => [
            'App\Listeners\SendEmailConfirmation',
        ],
        'App\Events\UserWasCreated'                => [
            'App\Listeners\AddVouchers',
            'App\Listeners\CreateCloudstackUser',
            'App\Listeners\SetupSecurityGroups',
        ],
        'App\Events\Cloudstack\NewAsyncJob'        => [
            'App\Listeners\Cloudstack\CheckJobStatus',
        ],
        'App\Events\Cloudstack\JobFinished'        => [
            'App\Listeners\Cloudstack\RecordActivity',
        ],
        'App\Events\Cloudstack\InstanceWasCreated' => [
            'App\Listeners\Cloudstack\NotifyUserAboutInstance',
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Event::listen('auth.login', function ($user, $remember) {
            // Record user login
            Activity::create(['subject_type' => 'User',
                              'subject_id'   => '',
                              'event'        => 'Logged In',
                              'user_id'      => $user->id,
                              'ip'           => Request::server('REMOTE_ADDR')
            ]);
        });
    }
}
