<?php namespace App\Http\Controllers;

use App\Events\Cloudstack\NewAsyncJob;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SnapshotMetadata;
use Cloudstack\CloudStackClient;
use Request as HttpRequest;
use App\Package;
use App\Repositories\InstanceRepo;
use App\TemplateGroup;
use App\SiteConfig;
use App\Zone;
use App\DiskType;
use Illuminate\Http\Request;
use App\Http\Requests\CreateInstanceRequest;
use Auth;

class InstanceController extends Controller
{

    // Default admin-level ACS connection
    private $acs;

    public function __construct(CloudStackClient $acs)
    {
        $this->middleware('auth');

        $this->acs = $acs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // Grab the domain we're operating under
        $domainid = SiteConfig::whereParameter('domainId')->first();

        // Get list of VM instances for this user
        $vms = $this->acs->listVirtualMachines(['account' => Auth::User()->email, 'domainid' => $domainid->data]);

        return view('instance.index')->with(compact('vms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $domainId = SiteConfig::whereParameter('domainId')->first()->data;

        // Get all active zones, packages and templates
        $zones = Zone::whereStatus('Enabled')->get();
        $packages = Package::all();
        $templates = TemplateGroup::all();
        $myTemplates = $this->acs->listTemplates(['account'        => Auth::User()->email,
                                                  'domainid'       => $domainId,
                                                  'templatefilter' => 'self']);
        $diskTypes = DiskType::all();
        $sshKeys = $this->acs->listSSHKeyPairs(['account'  => Auth::User()->email,
                                                'domainid' => $domainId]);

        $secGroups = $this->acs->listSecurityGroups(['account'  => Auth::User()->email,
                                                     'domainid' => $domainId]);

        return view('instance.create')->with(compact('zones', 'packages', 'templates', 'myTemplates', 'diskTypes', 'sshKeys', 'secGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * Inputs:
     * name: VM name
     * package: id of the package chosen
     * template: templateGroup ID
     *
     * @return Response
     */
    public function store(CreateInstanceRequest $request, InstanceRepo $repo)
    {
        try {
            $response = $repo->create($request);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        event(new NewAsyncJob($response->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));

        // Go back to progress page that will eventually send us to instance listing.
        return redirect()->route('progress', [$response->jobid]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $domainId = SiteConfig::whereParameter('domainId')->first()->data;

        // Find the one VM for $id.  listVMs returns an array of stdObjects representing virtualMachines.
        $vm = $this->acs->listVirtualMachines(['id' => $id])[0];
        $disk = $this->acs->listVolumes(['virtualmachineid' => $vm->id, 'listall' => 'true', 'type' => 'ROOT'])[0];
        $serviceOffering = $this->acs->listServiceOfferings(['id' => $vm->serviceofferingid])[0];
        $diskType = DiskType::whereTags($serviceOffering->tags)->first();

        $snapshots = $this->acs->listSnapshots(['volmueid' => $disk->id, 'account' => Auth::User()->email, 'domainid' => $domainId]);

        return view('instance.show')->with(compact('vm', 'disk', 'diskType', 'snapshots'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $vm = $this->acs->listVirtualMachines(['id' => $id])[0];

        return view('instance.edit')->with(compact('vm'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        // UNUSED
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $resp = $this->acs->destroyVirtualMachine(['id' => $id]);
        event(new NewAsyncJob($resp->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));
//        flash()->success('Instance destroyed.');

        /*
        Activity::create(['subject_type' => 'Instance',
                          'subject_id'   => $id,
                          'event'        => 'Destroyed',
                          'user_id'      => Auth::User()->id,
                          'ip'           => Request::server('REMOTE_ADDR')
        ]); */

        return $resp->jobid;
        //
//        return redirect()->route('instance.index');
    }

    public function start($id)
    {
        //
        $resp = $this->acs->startVirtualMachine(['id' => $id]);
        event(new NewAsyncJob($resp->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));

//        flash()->success('Instance started.');

        return redirect()->route('progress', [$resp->jobid]);
    }

    public function stop($id)
    {
        $resp = $this->acs->stopVirtualMachine(['id' => $id]);
        event(new NewAsyncJob($resp->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));

//        flash()->success('Instance stopped.');

//        return back();
        return redirect()->route('progress', [$resp->jobid]);
    }

    public function reboot($id)
    {
        $resp = $this->acs->rebootVirtualMachine(['id' => $id]);
        event(new NewAsyncJob($resp->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));

//        flash()->success('Instance rebooted.');

//        return back();
        return redirect()->route('progress', [$resp->jobid]);
    }

    public function recover($id)
    {
        $resp = $this->acs->recoverVirtualMachine(['id' => $id]);

        return redirect()->route('instance.show', [$resp->virtualmachine->id]);
    }

    public function snapshot($id)
    {
        $vm = $this->acs->listVirtualMachines(['id' => $id])[0];

        if (strcasecmp($vm->hypervisor, 'KVM') == 0) {
            if ($vm->state != 'Stopped') {
                return back()->withErrors('Please power off your instance before attempting to snapshot it.');
            } else {
                // Find root volume and snapshot that.
                try {
                    $disk = $this->acs->listVolumes(['virtualmachineid' => $vm->id, 'listall' => 'true', 'type' => 'ROOT'])[0];
                    $resp = $this->acs->createSnapshot(['account'  => Auth::User()->email,
                                                        'domain'   => SiteConfig::whereParameter('domainId')->first()->data,
                                                        'volumeid' => $disk->id]);
                } catch (\Exception $e) {
                    return back()->withErrors($e->getMessage());
                }
            }
        } else {
            try {
                $resp = $this->acs->createVMSnapshot(['id' => $id]);
            } catch (\Exception $e) {
                return back()->withErrors($e->getMessage());
            }
        }

        if (empty($resp->jobid)) {
            if ($resp->errorcode == 535) {
                return back()->withErrors('Your allotted secondary storage space has been exceeded.');
            }

            return back()->withErrors('We encountered an error while creating your snapshot.');
        }

        return redirect()->route('progress', [$resp->jobid]);
    }

    public function deleteSnapshot($id)
    {
        //
        $resp = $this->acs->deleteSnapshot(['id' => $id]);

        return $resp->jobid;
    }

    public function newTemplateFromSnapshot($id)
    {
        //
        return view('instance.newtemplate', ['snapshot' => $id]);
    }

    public function createTemplateFromSnapshot(Request $request, $id)
    {
        // Get metadata for this snapshot.
        $meta = SnapshotMetadata::find($id);

        // Get user's API credentials
        $user = current($this->acs->listUsers(['account'  => Auth::User()->email,
                                       'domainid' => SiteConfig::whereParameter('domainId')->first()->data]));

        // If null, generate them.
        if (empty($user->secretkey) || empty($user->apikey)) {
            $keys = $this->acs->registerUserKeys(['id' => $user->id]);
        }

        $apikey = (isset($keys->apikey)) ? $keys->apikey : $user->apikey;
        $secretkey = (isset($keys->secretkey)) ? $keys->secretkey : $user->secretkey;

        // Create ACS connector using the API credentials
        $user_acs = app('Cloudstack\CloudStackClient');
        $user_acs->setKeys($apikey, $secretkey);

        // Call createTemplate as the user
        $resp = $user_acs->createTemplate(['displaytext' => $request->name,
                                           'name'        => $request->name,
                                           'ostypeid'    => $meta->ostypeid,
                                           'snapshotid'  => $id]);

        return redirect()->route('progress', [$resp->jobid]);
    }

    public function resetPassword($id)
    {
        // Reset Password
        $response = $this->acs->resetPasswordForVirtualMachine(['id' => $id]);

        // Command is async
        event(new NewAsyncJob($response->jobid, Auth::User()->id, HttpRequest::server('REMOTE_ADDR')));

        return redirect()->route('progress', [$response->jobid]);
    }
}
