<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit()
    {
        //
        return view('admin.profile.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        //
        $this->validate($request, ['password' => 'required|confirmed|min:10']);

        $user = Auth::User();

        $user->password = bcrypt($request->password);
        flash()->success('Successfully updated password.');

        return redirect()->route('admin.home');
    }
}
