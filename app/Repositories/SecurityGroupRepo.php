<?php

namespace App\Repositories;

use App\Repositories\Eloquent\IngressRule;
use App\Repositories\Eloquent\SecurityGroup;

class SecurityGroupRepo {

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    public function create($name, $description)
    {
        return SecurityGroup::create(['name'        => $name,
                                      'description' => $description]);
    }

    public function destroy($id)
    {
        // Delete the security group and all of its rules.
        SecurityGroup::destroy($id);

        return true;
    }

    public function all()
    {
        // Get all security groups and their applicable rules.
        return SecurityGroup::with('ingressRules')->get();
    }

    public function find($id)
    {
        return SecurityGroup::with('ingressRules')->where('id', $id)->first();
    }

    /*
     * Functions for Ingress Rules
     */
    public function addIngressRule($secGroup, $cidr, $protocol, $data1, $data2)
    {
        $sg = SecurityGroup::find($secGroup);

        $data1Key = ($protocol == 'ICMP') ? 'icmp_type' : 'start_port';
        $data2Key = ($protocol == 'ICMP') ? 'icmp_code' : 'end_port';

        $rule = new IngressRule(['cidr'     => $cidr,
                                 'protocol' => $protocol,
                                 $data1Key  => $data1,
                                 $data2Key  => $data2]);

        $sg->ingressRules()->save($rule);

        return true;
    }

    public function destroyIngressRule($id)
    {
        IngressRule::destroy($id);

        return true;
    }
}