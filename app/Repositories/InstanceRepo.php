<?php
namespace App\Repositories;

use App\SiteConfig;
use App\TemplateGroup;
use Auth;
use App\Package;

class InstanceRepo
{

    private $acs;
    private $serviceOfferings;

    private function getServiceOfferings()
    {
        $this->serviceOfferings = $this->acs->listServiceOfferings();
    }

    public function findServiceOffering($resources)
    {
        // Grab a searchable list of service offerings.
        $searchableOfferings = $this->searchableServiceOfferings();

        $offering_id = array_search($resources, $searchableOfferings);

        if ($offering_id == false) {
        // No offering found.  Look for a customized offering with our disk requirements.
            foreach ($this->serviceOfferings as $offering => $key) {
                if (true === $key->iscustomized && false !== strpos($key->tags, $resources['diskType'])) {
                    // Found our offering
                    return $key->id;
                }
            }
        } else {
            return $offering_id;
        }
        return false;
    }

    private function searchableServiceOfferings()
    {
        if (!isset($this->serviceOfferings)) {
            $this->getServiceOfferings();
        }

        // Create an array we can array_search on.
        $serviceOfferings = [];

        foreach ($this->serviceOfferings as $offering) {
            if (false == $offering->iscustomized) {
                $serviceOfferings[$offering->id] = ['cpu'      => $offering->cpunumber,
                                                    'memory'   => $offering->memory,
                                                    'diskType' => $offering->tags];
            }
        }

        return $serviceOfferings;
    }

    public function __construct($acs)
    {
        $this->acs = $acs;
    }

    public function create($request)
    {
        /**
         * ACS requires the following for deployVirtualMachine():
         * serviceofferingid
         * templateid
         * zoneid
         * account
         * domainid
         * name
         * keypair (if needed)
         * rootdisksize (if allowed)
         * hypervisor (if specifying rootdisksize)
         */
        $instanceData = [];

        // Get site configuration.  We're looking for hypervisor, rootdiskresize, and domainId.
        $cfg = SiteConfig::all()->makeKVArray();

        // Did we receive a package or a custom configuration?
        if (isset($request['package'])) {
            $pkg = Package::find($request['package']);
            $instanceData['serviceofferingid'] = $this->findServiceOffering(['cpu'      => $pkg->cpu_number,
                                                                             'memory'   => $pkg->ram,
                                                                             'diskType' => $pkg->diskType->tags]);
        } else {
            $instanceData['serviceofferingid'] = $this->findServiceOffering(['cpu'      => $request->coreSlider,
                                                                             'memory'   => ($request->ramSlider * 1024),
                                                                             'diskType' => $request->diskType]);
        }

        // If we can't find a service offering, error out.
        if (empty($instanceData['serviceofferingid'])) {
            abort(500);
        }

        if (isset($request->myTemplate)) {
        // Template is one created by the user.
            $instanceData['templateid'] = $request->myTemplate;
        } else {
            // Find the template we need.
            $templateGroup = TemplateGroup::find($request['template']);

            if ('FALSE' == $cfg['rootdiskresize']) {
            // Size can come from a package or a custom size slider.
                $disk_size = (isset($request['package'])) ? $pkg->disk_size : stristr($request['hdSlider'], ' ', true);

                // Find the specific size of template
                $templateGroup->templates->each(function ($tpl) use ($disk_size, &$instanceData) {
                    if ($tpl->size == $disk_size) {
                        $instanceData['templateid'] = $tpl->template_id;
                    }
                });
            } else if ('TRUE' == $cfg['rootdiskresize']) {
            // We don't care about template size.  We resize whatever we need.
                // Grab the template ID of the first (only) template in the group.
                $instanceData['templateid'] = $templateGroup->templates->first()->template_id;

                // If we have a package get the disk size from there.  If not we have a custom size.
                $instanceData['rootdisksize'] = (isset($request['package'])) ? $pkg->disk_size : $request['disk_size'];

                // We also need to set the hypervisor type.
                $instanceData['hypervisor'] = $cfg['hypervisor'];
            }
        }

        // Error out if we don't find a template
        if (empty($instanceData['templateid'])) {
            abort(500);
        }

        // Get the user object
        $user = Auth::User();

        // Add a network interface to the system
        $networks = $this->acs->listNetworks(['canusefordeploy' => 'true', 'account' => $user->email, 'domainid' => $cfg['domainId']]);

        // !!REVISE!!
        $instanceData['networkids'] = $networks[0]->id;

        // Select a security group for the instance
        $instanceData['securitygroupids'] = $request->secGroup;

        // Name the new instance
        $instanceData['name'] = $request['name'];

        // Add user data to the request
        $instanceData['account'] = $user->email;
        $instanceData['domainid'] = $cfg['domainId'];

        // What zone is being deployed to?
        $instanceData['zoneid'] = $request['zone'];

        // Do we need to add a keypair to the system?
        if (isset($request['keypair'])) {
            $instanceData['keypair'] = $request['keypair'];
        }

        return $this->acs->deployVirtualMachine($instanceData);
    }

    public function delete()
    {
    }
}
