<?php namespace App\Repositories\Contracts;


interface DNSInterface {

    public function createDomain($domain);

    public function deleteDomain($domain);

    public function createRecord($host, $type, $target = null, $priority = null, $port = null, $weight = null);

    public function editRecord($oldhost, $oldtype, $host, $type, $target = null, $priority = null, $port = null, $weight = null);

    public function deleteRecord($host, $type);

}