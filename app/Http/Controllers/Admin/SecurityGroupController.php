<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\SecurityGroupRepo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SecurityGroupController extends Controller {

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('setupComplete');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SecurityGroupRepo $repo)
    {
        //
        $groups = $repo->all();

        return view('admin.securitygroup.index', ['groups' => $groups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.securitygroup.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SecurityGroupRepo $repo)
    {
        //
        $this->validate($request, ['name' => 'required']);

        $repo->create($request->name, $request->description);

        flash()->success("Created new security group: $request->name.");

        return redirect()->route('admin.sg.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(SecurityGroupRepo $repo, $id)
    {
        //
        $group = $repo->find($id);

        return view('admin.securitygroup.show', ['group' => $group]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Not used.
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Not used.
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecurityGroupRepo $repo, $id)
    {
        //
        $repo->destroy($id);
        return 1;
    }

    public function addIngressRule(Request $request, SecurityGroupRepo $repo, $id)
    {
        // Add ingress rule to Security Group $id.
        $this->validate($request, ['cidr'      => 'required',
                                   'protocol'  => 'required|in:TCP,UDP,ICMP',
                                   'icmpType'  => 'numeric',
                                   'icmpCode'  => 'numeric',
                                   'startPort' => 'numeric',
                                   'endPort'   => 'numeric']);

        $data1 = ($request->protocol == 'ICMP') ? $request->icmpType : $request->startPort;
        $data2 = ($request->protocol == 'ICMP') ? $request->icmpCode : $request->endPort;

        $repo->addIngressRule($id, $request->cidr, $request->protocol, $data1, $data2);

        return redirect()->route('admin.sg.show', $id);
    }

    public function deleteIngressRule(SecurityGroupRepo $repo, $id)
    {
        //
        $repo->destroyIngressRule($id);

        return 1;
    }
}
