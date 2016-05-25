<?php namespace App\Http\Controllers\Settings;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ActivityController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

	//
    public function activity()
    {
        $events = Auth::User()->activity;

        return view('settings.activity')->with(compact('events'));
    }
}
