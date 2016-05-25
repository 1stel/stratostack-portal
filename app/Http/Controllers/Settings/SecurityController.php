<?php namespace App\Http\Controllers\Settings;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SiteConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller {

    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
        $acs_id = Auth::User()->email;
        $domain = SiteConfig::whereParameter('domainId')->first();

        $keys = app('Cloudstack\CloudStackClient')->listSSHKeyPairs(['account' => $acs_id,
                                                    'domainid'  => $domain->data]);
//dd($keys);
        return view('settings.security')->with(compact('keys'));
    }
}
