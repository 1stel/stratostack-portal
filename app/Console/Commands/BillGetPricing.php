<?php namespace App\Console\Commands;

use DB;
use App\ElementCost;
use App\SiteConfig;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BillGetPricing extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bill:getPricing';

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
        // Setup HTTP request for the records
        $url = SiteConfig::whereParameter('recordsUrl')->first()->data;
        $client = new Client(['base_uri' => $url]);

        $data = $client->get("api/getPricing");
        $priceData = json_decode($data->getBody());

        $priceMethod = SiteConfig::firstOrCreate(['parameter' => 'priceMethod']);
        $priceMethod->data = $priceData->priceMethod;
        $priceMethod->save();

        DB::statement('TRUNCATE element_costs');

        foreach ($priceData->prices as $price)
        {
            if ($priceData->priceMethod == 'fixedRatio')
            {
                $frPrice = SiteConfig::firstOrCreate(['parameter' => $price->parameter]);
                $frPrice->data = $price->data;
                $frPrice->save();
            }
            else if ($priceData->priceMethod == 'elementPrice')
            {
                ElementCost::create(['element'       => $price->element,
                                     'quantity'      => $price->quantity,
                                     'quantity_type' => $price->quantity_type,
                                     'price'         => $price->price]);
            }
        }
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
