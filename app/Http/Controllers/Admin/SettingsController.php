<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\SiteConfig;

class SettingsController extends Controller {

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
    }

    public function index()
    {
        $settings = SiteConfig::all()->makeKVArray();

        return view('admin.settings.index')->with(compact('settings'));
    }

    public function save(Request $request)
    {
        $validationArr = ['recordsUrl'         => 'required|url',
                          'domainId'           => 'required',
                          'hypervisor'         => 'required',
                          'rootdiskresize'     => 'required',
                          'creditLimit'        => 'required',
                          'defaultPaymentType' => 'required',
                          'vouchers'           => 'required|numeric',
                          'voucherAmount'      => 'required|numeric',
                          'hoursInMonth'       => 'required|numeric',
                          'grandfatherPricing' => 'required',
                          'dnsServer'          => 'required',
                          'dnsApiKey'          => 'required'];

        $this->validate($request, $validationArr);

        foreach ((array_keys($validationArr)) as $setting)
        {
            $cfgSetting = SiteConfig::whereParameter($setting)->firstOrFail();

            if ($cfgSetting->data != $request->$setting)
            {
                $cfgSetting->data = $request->$setting;
                $cfgSetting->save();
            }
        }

        flash()->success('Settings successfully saved.');

        return redirect('admin/settings');
    }

    public function edit()
    {
        $settings = SiteConfig::all()->makeKVArray();

        return view('admin.settings.edit')->with(compact('settings'));
    }

    public function updateCloud()
    {
        $recordsServer = SiteConfig::whereParameter('recordsUrl')->first();

        $client = new Client(['base_uri' => $recordsServer->data]);

        $data = $client->get("api/getPricing");
        $pricing = json_decode($data->getBody());

        $data = $client->get("api/getResourceLimits");
        $resourceLimits = json_decode($data->getBody());

        foreach ($resourceLimits as $resource => $limit)
        {
            $dbEntry = SiteConfig::firstOrCreate(['parameter' => 'RL' . $resource]);
            $dbEntry->data = $limit;
            $dbEntry->save();
        }

        flash()->success('Cloud settings successfully updated.');

        return redirect()->route('admin.settings.index');
    }
}
