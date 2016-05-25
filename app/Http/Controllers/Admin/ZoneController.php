<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Zone;

class ZoneController extends Controller {

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
    }

    public function index()
    {
        // Grab all of our zones.  If there are none, import the list from our cloud provider.
        $zones = Zone::all();

        if ($zones->count() == 0)
        {
            unset($zones);

            // Store the zones in the database, defaulting them to disabled.
            $providerZones = app('Cloudstack\CloudStackClient')->listZones();

            foreach ($providerZones as $providerZone)
            {
                Zone::create(['zone_id' => $providerZone->id,
                              'name'    => $providerZone->name,
                              'status'  => 'Disabled']);
            }

            $zones = Zone::all();
        }

        return view('admin.zone.index')->with(compact('zones'));
    }

    public function edit($id)
    {
        $zone = Zone::findOrFail($id);

        return view('admin.zone.edit')->with(compact('zone'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, ['name'         => 'required',
                                   'display_name' => 'required',
                                   'status'       => 'required|in:Disabled,Enabled']);

        $zone = Zone::findOrFail($id);

        $zone->name = $request->name;
        $zone->display_name = $request->display_name;
        $zone->status = $request->status;
        $zone->save();

        return redirect()->route('admin.zone.index');
    }
}