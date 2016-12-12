<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $users = User::whereAccess('Admin')->get();

        return view('admin.user.index')->with(compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, ['name'     => 'required',
                                   'email'    => 'required|email',
                                   'password' => 'required|confirmed|min:10']);

        User::create(['name'                => $request->name,
                      'email'               => $request->email,
                      'password'            => bcrypt($request->password),
                      'access'              => 'Admin',
                      'paymentTypeOverride' => 'PostPay',
                      'acs_id'              => 0,
                      'credit'              => 0,
                      'bill_date'           => '0000-00-00']);

        return redirect()->route('admin.user.index');
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
        $user = User::findOrFail($id);

        return view('admin.user.edit')->with(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        //
        $this->validate($request, ['name'     => 'required',
                                   'email'    => 'required|email',
                                   'password' => 'confirmed|min:10']);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        flash()->success('Updated user information.');
        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        User::destroy($id);
        flash()->success('Administrative user deleted.');

        return 1;
    }
}
