<?php

namespace App\Listeners;

use App\SiteConfig;
use App\Events\UserWasCreated;
use App\Repositories\SecurityGroupRepo;
use CloudStack\CloudStackClient;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetupSecurityGroups implements ShouldQueue
{
    protected $repo;
    private $acs;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SecurityGroupRepo $repo, CloudStackClient $acs)
    {
        //
        $this->repo = $repo;
        $this->acs = $acs;
    }

    /**
     * Handle the event.
     *
     * @param  UserWasCreated $event
     * @return void
     */
    public function handle(UserWasCreated $event)
    {
        // $event contains a user object at $event->user
        $groups = $this->repo->all();
        $domainId = SiteConfig::whereParameter('domainId')->first();

        foreach ($groups as $group) {
            // Add each group to the user.
            if (strcasecmp($group->name, 'default') != 0) {
                // Default group doesn't need creating.
                $this->acs->createSecurityGroup([
                    'name' => $group->name,
                    'description' => $group->description,
                    'account' => $event->user->email,
                    'domainid' => $domainId->data
                ]);

                sleep(5); // Make sure the group gets created.
            }

            foreach ($group->ingressRules as $rule) {
                if ($rule->protocol == 'ICMP') {
                    $this->acs->authorizeSecurityGroupIngress([
                        'account' => $event->user->email,
                        'domainid' => $domainId->data,
                        'cidrlist' => $rule->cidr,
                        'icmpcode' => $rule->icmp_code,
                        'icmptype' => $rule->icmp_type,
                        'protocol' => $rule->protocol,
                        'securitygroupname' => $group->name,
                    ]);
                } else {
                    $this->acs->authorizeSecurityGroupIngress([
                        'account' => $event->user->email,
                        'domainid' => $domainId->data,
                        'cidrlist' => $rule->cidr,
                        'startport' => $rule->start_port,
                        'endport' => $rule->end_port,
                        'protocol' => $rule->protocol,
                        'securitygroupname' => $group->name,
                    ]);
                }
            }
        }
    }
}
