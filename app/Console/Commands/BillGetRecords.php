<?php namespace App\Console\Commands;

use Config;
use App\SiteConfig;
use App\UsageDisk;
use App\UsageGeneral;
use App\UsageInstance;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BillGetRecords extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bill:getRecords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        //
//        $siteCfg = SiteConfig::all()->makeKVArray();

        $siteCfg = ['domainId'           => SiteConfig::whereParameter('domainId')->first()->data,
                    'apiKey'             => Config::get('cloud.mgmtServer.apiKey'),
                    'secretKey'          => Config::get('cloud.mgmtServer.secretKey'),
                    'lastBillRecordDate' => SiteConfig::whereParameter('lastBillRecordDate')->first()->data];


        // Setup HTTP request for the records
        $url = SiteConfig::whereParameter('recordsUrl')->first()->data;
        $client = new Client(['base_uri' => $url]);

        $data = $client->get("api/getRecords/domainid/{$siteCfg['domainId']}/apiKey/{$siteCfg['apiKey']}/secretKey/{$siteCfg['secretKey']}/lastDate/{$siteCfg['lastBillRecordDate']}");

        $records = json_decode($data->getBody());

        foreach ($records->instances as $instanceRecord) {
            $keys = ['zoneId', 'accountId', 'vm_name', 'usage', 'vmInstanceId', 'serviceOfferingId',
                'templateId', 'cpuNumber', 'cpuSpeed', 'memory', 'startDate', 'endDate'];
            $data = [];

            foreach ($keys as $key) {
                $data[$key] = $instanceRecord->$key;
            }

            UsageInstance::create($data);
            unset($data);
        }

        foreach ($records->general as $generalRecord) {
            $keys = ['zoneId', 'accountId', 'type', 'usage', 'vmInstanceId', 'templateId', 'startDate', 'endDate'];
            $data = [];

            foreach ($keys as $key) {
                $data[$key] = $generalRecord->$key;
            }

            UsageGeneral::create($data);
            unset($data);
        }

        foreach ($records->disk as $diskRecord) {
            $keys = ['zoneId', 'accountId', 'volumeId', 'size', 'type', 'tags', 'usage', 'vmInstanceId', 'startDate', 'endDate'];
            $data = [];

            foreach ($keys as $key) {
                $data[$key] = $diskRecord->$key;
            }

            UsageDisk::create($data);
            unset($data);
        }

        // Set last bill record date to today.
//        SiteConfig::where('parameter', '=', 'lastBillRecordDate')->update(['data' => date('Y-m-d')]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//			['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
//			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
