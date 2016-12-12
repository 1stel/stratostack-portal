<?php namespace App\Http\Controllers\Settings;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SiteConfig;
use Cloudstack\CloudStackClient;
use Illuminate\Http\Request;
use Auth;
use Mail;

class ProfileController extends Controller
{

    //
    private $acs;
    
    public function __construct(CloudStackClient $acs)
    {
        $this->middleware('auth');
        
        $this->acs = $acs;
    }

    public function profile()
    {
        //
        $user = Auth::User();

        return view('settings.profile')->with(compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::User();
        $domainId = SiteConfig::whereParameter('domainId')->first();

        $this->validate($request, [
            'name'         => 'required|max:255',
            'email'        => 'required|email|max:255|unique:users,id,' . $user->id,
            'password'     => 'required_with:new_password|min:6',
            'new_password' => 'confirmed|min:6'
        ]);

        if ($user->name != $request['name']) {
        // Change name in DB
            $user->name = $request['name'];

            // Set the name for ACS
            list ($userData['firstname'], $userData['lastname']) = explode(' ', $request['name'], 2);
        }

        if ($user->email != $request['email']) {
        // Copy the email for lookup purposes
            $origEmail = $user->email;

            // Update the email data in the DB
            $user->email = $request['email'];

            // Two changes are necessary in ACS: at the User and Account levels
            $userData['email'] = $request['email'];
            $userData['username'] = $request['email'];
            $accountData['newname'] = $request['email'];
        }

        if (isset($request['new_password']) && '' != $request['new_password']) {
            $user->password = bcrypt($request['new_password']);

            $userData['password'] = $request['new_password'];

            Mail::send('emails.password_changed', [], function ($m) use ($user) {
                $m->from('support@stratostack.com', 'StratoSTACK');
                $m->to($user->email, $user->name)->subject('Password changed');
            });
        }

        if (!isset($userData) && !isset($accountData)) {
        // May do something here later.
        }

        // Check to see if we have changes that need to touch the Cloudstack system.
        if (isset($userData)) {
            $userData['id'] = $user->acs_id;
            $userRequest = $this->acs->updateUser($userData);
        }

        if (isset($accountData)) {
            $accountData['account'] = $origEmail;
            $accountData['domainid'] = $domainId->data;
            $accountRequest = $this->acs->updateAccount($accountData);
        }

        // Update the database
        $user->save();

        flash()->success('Updated user profile.');

        // Add message indicating save.
        return redirect()->route('settings.profile');
    }
}
