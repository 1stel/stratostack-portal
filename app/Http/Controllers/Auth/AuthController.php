<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Mail;
use Validator;
use App\User;
use App\SiteConfig;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard $auth
     * @param  \Illuminate\Contracts\Auth\Registrar $registrar
     * @return void
     */
    public function __construct()
    {
//		$this->auth = $auth;
//		$this->registrar = $registrar;

        $this->middleware('guest', ['except' => ['getLogout', 'getValidationNeeded']]);
    }

    public function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    public function create(array $data)
    {
        $paymentType = SiteConfig::whereParameter('defaultPaymentType')->first();
//        system("curl --data \"[StratoSTACK] {$data['email']} registered as a new user.\" -s 'https://1stel.slack.com/services/hooks/slackbot?token=oxHCUBstAR4y9p9dvj8FE5Jr&channel=%23devops'");

        $user = User::create([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'password'            => bcrypt($data['password']),
            'access'              => 'User',
            'paymentTypeOverride' => $paymentType->data,
            'email_token'         => bin2hex(openssl_random_pseudo_bytes(10))
        ]);

        event(new \App\Events\UserHasRegistered($user));

        return $user;
    }

    public function getValidationNeeded()
    {
        return view('please-validate');
    }
}
