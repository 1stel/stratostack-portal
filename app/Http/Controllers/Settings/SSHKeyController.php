<?php namespace App\Http\Controllers\Settings;

use App\Activity;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SiteConfig;
use Cloudstack\CloudStackClient;
use Illuminate\Http\Request;
use Auth;

class SSHKeyController extends Controller
{

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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Requests\AddSSHKeyRequest $request)
    {
        $domain = SiteConfig::whereParameter('domainId')->first();

        // Call the ACS API and add the ssh key to the necessary account
        $result = $this->acs->registerSSHKeyPair(['account'   => Auth::User()->email,
                                                         'domainid'  => $domain->data,
                                                         'name'      => $request['name'],
                                                         'publickey' => $request['publicKey']
        ]);

        if (!empty($result->errorcode)) {
            return back()->withErrors($result->errortext)->withInput();
        }

        Activity::create(['subject_type' => 'SSH Key',
                          'subject_id'   => 0,
                          'event'        => 'Created',
                          'user_id'      => Auth::User()->id,
                          'ip'           => $request->server('REMOTE_ADDR')
        ]);

        return redirect()->route('settings.security');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($name)
    {
        $domain = SiteConfig::whereParameter('domainId')->first();

        // Call the ACS API and add the ssh key to the necessary account
        $result = $this->acs->deleteSSHKeyPair(['account'   => Auth::User()->email,
                                                         'domainid'  => $domain->data,
                                                         'name'      => $name
        ]);

        Activity::create(['subject_type' => 'SSH Key',
                          'subject_id'   => 0,
                          'event'        => 'Deleted',
                          'user_id'      => Auth::User()->id,
                          'ip'           => $request->server('REMOTE_ADDR')
        ]);

        return 1;
    }
}
