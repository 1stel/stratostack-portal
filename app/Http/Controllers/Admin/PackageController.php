<?php namespace App\Http\Controllers\Admin;

use App\DiskType;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SiteConfig;
use Illuminate\Http\Request;
use App\Http\Requests\PackageRequest;
use App\Package;

class PackageController extends Controller {

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
        $packages = Package::with('diskType')->get();

        return view('admin.package.index')->with(compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        // Gather resource limits
        $cpuData = SiteConfig::whereParameter('RLcpu_number')->first();
        $ramData = SiteConfig::whereParameter('RLram')->first();
        $diskData = DiskType::all();

        $cpuLimit = $cpuData->data;
        $ramLimit = [];
        $disks = [];

        for ($i = 1; $i <= $ramData->data; $i++)
            $ramLimit[$i * 1024] = $i;

        foreach ($diskData as $disk)
        {
            $disks[$disk->id] = $disk->display_text;
        }

        return view('admin.package.create')->with(compact('cpuLimit', 'ramLimit', 'disks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(PackageRequest $request)
    {
        //
        $pkg = Package::create(['cpu_number'          => $request->cpu_number,
                                'ram'                 => $request->ram,
                                'disk_size'           => $request->disk_size,
                                'disk_type'           => $request->disk_type,
                                'price'               => $request->price,
                                'tic'                 => '30700', // !!REVISE!!
//                                'tic'                 => $request->tic,
                                'paymentTypeOverride' => $request->paymentTypeOverride
        ]);

        return redirect('/');
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
    public function destroy($id)
    {
        //
        Package::destroy($id);

        return 1;
    }

}
