<?php

namespace App\Repositories;

use App\Repositories\Eloquent\Domain;
use App\Repositories\Eloquent\DomainRecord;
use Auth;

class DNSRepository
{

    private $server;

    private function validateRecord($type, $target)
    {
        // Validate whether the supplied $target is proper input for DNS record $type.

        if (in_array($type, ['CNAME', 'NS', 'SPF', 'SRV'])) {
        // $target can be a domain name
            return preg_match('/^([a-z0-9][a-z0-9-]{0,62}\.)+([a-z]{2,4})$/i', $target);
        } else if (in_array($type, ['A', 'AAAA', 'WKS', 'LOC'])) {
        // $target needs to be ip address
            return filter_var($target, FILTER_VALIDATE_IP);
        } else if (in_array($type, ['MX'])) {
        // $target can be domain name or ip address
            return filter_var($target, FILTER_VALIDATE_IP) || preg_match('/^([a-z0-9][a-z0-9-]{0,62}\.)+([a-z]{2,4})$/i', $target);
        } else if (in_array($type, ['TXT'])) {
        // No validation performed on TXT records.
            return true;
        } else {
            return false;
        }
    }

    public function __construct(\App\Repositories\Contracts\DNSInterface $server)
    {
        $this->server = $server;
    }

    public function allDomains()
    {
        return Domain::where('user_id', '=', Auth::User()->id)->get();
    }

    public function findDomain($id)
    {
        return Domain::where('user_id', '=', Auth::User()->id)->whereId($id)->first();
    }

    public function allRecords()
    {
        return DomainRecord::where('user_id', '=', Auth::User()->id)->get();
    }

    public function findRecord($id)
    {
        return DomainRecord::where('user_id', '=', Auth::User()->id)->whereId($id)->first();
    }

    public function createDomain($domain)
    {
        $this->server->createDomain($domain);

        Domain::create(['name' => $domain, 'user_id' => Auth::User()->id]);
    }

    public function deleteDomain($id)
    {
        $domain = Domain::find($id);

        if ($domain->user_id != Auth::User()->id) {
            throw new \Exception('Access to domain denied.');
        }

        // Delete all of the domain's records
        $records = $domain->records;

        foreach ($records as $record) {
            $this->deleteRecord($record->id);
        }

        // Delete the domain
        $this->server->deleteDomain($domain->name);
        $domain->delete();

        return true;
    }

    public function createRecord($data = [])
    {
        // data will contain:
        // Required: domainId, hostname, type
        // Optional: target, priority, port and weight

        $target = (isset($data['target'])) ? $data['target'] : null;
        $priority = (isset($data['priority'])) ? $data['priority'] : null;
        $port = (isset($data['port'])) ? $data['port'] : null;
        $weight = (isset($data['weight'])) ? $data['weight'] : null;

        $domain = Domain::find($data['domainId']);

        if ($domain->user_id != Auth::User()->id) {
            throw new \Exception('Access to domain denied.');
        }

        if (!$this->validateRecord($data['type'], $data['target'])) {
            throw new \Exception('Invalid destination for the chosen record type.');
        }

        $recordQuery = DomainRecord::where('domain_id', '=', $data['domainId'])
            ->where('name', '=', $data['hostname'])
            ->where('type', '=', $data['type']);

        if (!empty($target)) {
            $recordQuery = $recordQuery->where('target', '=', $target);
        }

        if (!empty($priority)) {
            $recordQuery = $recordQuery->where('priority', '=', $priority);
        }

        if (!empty($port)) {
            $recordQuery = $recordQuery->where('port', '=', $port);
        }

        if (!empty($weight)) {
            $recordQuery = $recordQuery->where('weight', '=', $weight);
        }

        $record = $recordQuery->first();

        if ($record instanceof DomainRecord) {
            throw new \Exception('Unable to create duplicate record.');
        }

        // If we're making an MX or NS record, name is the domain.
        $hostname = (in_array($data['type'], ['MX', 'NS'])) ? $domain->name : $data['hostname'] . '.' . $domain->name;

        $this->server->createRecord(
            $hostname,
            $data['type'],
            $target,
            $priority,
            $port,
            $weight
        );

        DomainRecord::create(['domain_id' => $data['domainId'],
                              'name'      => $data['hostname'],
                              'type'      => $data['type'],
                              'target'        => $target,
                              'priority'  => $priority,
                              'port'      => $port,
                              'weight'    => $weight,
                              'user_id'   => Auth::User()->id]);

        return true;
    }

    public function editRecord($recordId, $data = [])
    {
        $record = DomainRecord::find($recordId);

        if ($record->user_id != Auth::User()->id) {
            throw new \Exception('Access to record denied.');
        }

        if (!$this->validateRecord($data['type'], $data['target'])) {
            throw new \Exception('Invalid destination for the chosen record type.');
        }

        $domain = $record->domain->name;

        $hostname = (in_array($data['type'], ['MX', 'NS'])) ? $domain : $data['hostname'] . '.' . $domain;

        $target = (isset($data['target'])) ? $data['target'] : null;
        $priority = (isset($data['priority'])) ? $data['priority'] : null;
        $port = (isset($data['port'])) ? $data['port'] : null;
        $weight = (isset($data['weight'])) ? $data['weight'] : null;


        // Update database first, because it checks for existing records before making the call.
            $record->name = $data['hostname'];

        $record->type = $data['type'];
        $record->target = $target;
        $record->priority = $priority;
        $record->port = $port;
        $record->weight = $weight;
        $record->save();

        $this->server->editRecord(
            "{$record->name}.{$domain}",
            $record->type,
            $hostname,
            $data['type'],
            $target,
            $priority,
            $port,
            $weight
        );

        return $record;
    }

    public function deleteRecord($recordId)
    {
        $record = DomainRecord::find($recordId);

        if ($record->user_id != Auth::User()->id) {
            throw new \Exception('Access to record denied.');
        }

        $this->server->deleteRecord($record->name . '.' . $record->domain->name, $record->type);
        $record->delete();
    }
}
