<?php namespace App\DNS;

use Config;
use App\Repositories\Contracts\DNSInterface;
use App\Repositories\Eloquent\Domain;
use App\Repositories\Eloquent\DomainRecord;
use GuzzleHttp\Client;

class PowerDNSAPI implements DNSInterface
{

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createDomain($domain)
    {
        //
        $data = ['name'        => $domain,
                 'kind'        => 'Native',
                 'masters'     => [],
                 'nameservers' => [Config::get('powerdns.ns1'), Config::get('powerdns.ns2')]
        ];

        try {
            $this->client->post('servers/localhost/zones', ['json' => $data]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function deleteDomain($domain)
    {
        try {
            $this->client->delete('servers/localhost/zones/' . $domain);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function createRecord($host, $type, $target = null, $priority = null, $port = null, $weight = null)
    {
        $domain = $this->getDomain($host);

        $content = $this->makeContent($type, $target, $priority, $port, $weight);

        $records = [
            ['content'  => $content,
             'disabled' => false,
             'name'     => $host,
             'ttl'      => 86400,
             'type'     => $type
            ]
        ];

        $domainId = Domain::where('name', '=', $domain)->first()->id;

        // Do we need to add any additional records into this call?
        $existingRecordsQuery = DomainRecord::where('type', '=', $type)->where('target', '!=', $target)->where('domain_id', '=', $domainId);

        if (in_array($type, ['MX', 'NS'])) {
            $existingRecordsQuery = $existingRecordsQuery->whereNull('name');
        } else if ($type == 'SRV') {
            $existingRecordsQuery = $existingRecordsQuery->where('name', '=', substr($host, 0, strpos($host, '.', strpos($host, '.') + 1)));
        } else {
            $existingRecordsQuery = $existingRecordsQuery->where('name', '=', current(explode('.', $host)));
        }

        $existingRecords = $existingRecordsQuery->get();

        if (count($existingRecords) > 0) {
            foreach ($existingRecords as $existingRecord) {
                $records[] = ['content'  => $this->makeContent(
                    $existingRecord->type,
                    $existingRecord->target,
                    $existingRecord->priority,
                    $existingRecord->port,
                    $existingRecord->weight
                ),
                              'disabled' => false,
                              'name'     => $host,
                              'ttl'      => 86400,
                              'type'     => $existingRecord->type
                ];
            }
        }

        $data = ['rrsets' => [['name'       => $host,
                               'type'       => $type,
                               'changetype' => 'REPLACE',
                               'records'    => $records
        ]]];

        try {
            $response = $this->client->patch('servers/localhost/zones/' . $domain, ['json' => $data]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    public function editRecord($oldhost, $oldtype, $host, $type, $target = null, $priority = null, $port = null, $weight = null)
    {
        // The syntax to update is the same as to add, but if the record name changed we must delete the old record.
        if ($oldhost != $host || $oldtype != $type) {
            $this->deleteRecord($oldhost, $oldtype);
        }

        // Create does a replace, so it'll overwrite the IP entry on the existing record.
        $this->createRecord($host, $type, $target, $priority, $port, $weight);
    }

    public function deleteRecord($host, $type)
    {
        //
        $domain = $this->getDomain($host);

        $data = ['rrsets' => [['name'       => $host,
                               'type'       => $type,
                               'changetype' => 'DELETE'
        ]]];

        try {
            $response = $this->client->patch('servers/localhost/zones/' . $domain, ['json' => $data]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    private function getDomain($host)
    {
        $hostParts = explode('.', $host);
        $hostCount = count($hostParts);
        if ($hostCount > 2) {
            return $hostParts[$hostCount - 2] . '.' . $hostParts[$hostCount - 1];
        } else {
            return $host;
        }
    }

    private function makeContent($type, $target, $priority = null, $port = null, $weight = null)
    {
        switch ($type) {
            case 'A':
            case 'AAAA':
            case 'CNAME':
            case 'TXT':
            case 'NS':
                return $target;

            case 'MX':
                return "$priority $target";

            case 'SRV':
                return "$priority $weight $port $target";
        }
    }
}
