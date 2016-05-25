<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DNS;

class DNSController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $domains = DNS::allDomains();

        return view('dns.index')->with(compact('domains'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('dns.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Requests\DNS\StoreDomainRequest $request)
    {
        //
        DNS::createDomain($request->name);

        flash()->success("$request->name added to DNS.");

        return redirect()->route('dns.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        // NOT CURRENTLY IN USE
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
        $domain = DNS::findDomain($id);
        $recordTypes = ['A', 'AAAA', 'CNAME', 'MX', 'NS', 'TXT', 'SRV'];

        return view('dns.edit')->with(compact('domain', 'recordTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        // NOT CURRENTLY IN USE
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
        DNS::deleteDomain($id);

        return 1;
    }

    /**
     * Store a newly created DNS record.
     *
     * @return Response
     */
    public function storeRecord(Requests\DNS\StoreRecordRequest $request)
    {
//        dd($request);
        $data = ['domainId' => $request->domain_id,
                 'hostname' => $request->name,
                 'type'     => $request->type,
                 'target'   => $request->target];

        if (isset($request->priority))
            $data['priority'] = $request->priority;

        if (isset($request->port))
            $data['port'] = $request->port;

        if (isset($request->weight))
            $data['weight'] = $request->weight;

        try
        {
            DNS::createRecord($data);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        flash()->success("Added $request->type record $request->name to DNS.");

        return redirect()->route('dns.edit', $request->domain_id);
    }

    public function editRecord($id)
    {
        $record = DNS::findRecord($id);

        return view('dns.editRecord')->with(compact('record'));
    }

    public function updateRecord(Requests\DNS\UpdateRecordRequest $request, $id)
    {
        $data = ['hostname' => $request->name,
                 'type'     => $request->type,
                 'target'   => $request->target];

        if (isset($request->priority))
            $data['priority'] = $request->priority;

        if (isset($request->port))
            $data['port'] = $request->port;

        if (isset($request->weight))
            $data['weight'] = $request->weight;

        $record = DNS::editRecord($id, $data);

        flash()->success("Successfully updated DNS record.");

        return redirect()->route('dns.edit', $record->domain_id);
    }

    public function destroyRecord($id)
    {
        DNS::deleteRecord($id);

        return 1;
    }

}
