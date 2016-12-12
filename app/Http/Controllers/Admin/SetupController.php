<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Config;
use App\SiteConfig;
use Illuminate\Http\Request;
use Cloudstack\CloudStackClient;

class SetupController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin.setup.index');
    }

    public function save(Request $request)
    {
        $this->validate($request, ['recordsUrl' => 'required|url',
                                   'domainId'  => 'required']);

        $acsCfg = Config::get('cloud.mgmtServer');
        $acs = new CloudStackClient($acsCfg['url'], $acsCfg['apiKey'], $acsCfg['secretKey']);

        if (is_array($acs)) {
            return back()->withErrors(['message' => $acs['error']]);
        }

        $zones = $acs->listZones();

        if (isset($zones->errorcode)) {
            return back()->withErrors(['message' => 'Unable to verify user credentials.']);
        }

        // We've received some data back now, so its ok to save the credentials.


        $hypervisors = [];
        try {
            $zones = $acs->listZones();

            foreach ($zones as $zone) {
                $hyperQuery = $acs->listHypervisors(['zoneid' => $zone->id]);

                foreach ($hyperQuery as $hypervisor) {
                    if (!in_array($hypervisor->name, $hypervisors)) {
                        $hypervisors[] = $hypervisor->name;
                    }
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        if (count($hypervisors) > 1) {
            return back()->withErrors(['message' => 'More than one hypervisor type detected.  Multiple hypervisor types not supported.']);
        } else {
            $hypervisor = $hypervisors[0];
        }

        $recordsUrl = SiteConfig::whereParameter('recordsUrl')->firstOrFail();
        $recordsUrl->data = $request->recordsUrl;
        $recordsUrl->save();

        $dbHypervisor = SiteConfig::whereParameter('hypervisor')->firstOrFail();
        $dbHypervisor->data = $hypervisor;
        $dbHypervisor->save();

        $domainId = SiteConfig::whereParameter('domainId')->firstOrFail();
        $domainId->data = $request->domainId;
        $domainId->save();

        $setup = SiteConfig::whereParameter('setupComplete')->first();
        $setup->data = 'true';
        $setup->save();

        return redirect('/admin/');
    }
}
