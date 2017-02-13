<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SnapshotMetadata;
use App\Zone;
use DNS;
use Auth;
use Config;
use App\SiteConfig;
use App\Package;
use App\TemplateGroup;
use Illuminate\Http\Request;

class APIController extends Controller
{

    public function receiveNotification(Request $request)
    {
        //
        $this->validate($request, ['apikey'   => 'required',
                                   'account'  => 'required',
                                   'event'    => 'required',
                                   'uuid'     => 'required',
                                   'ostypeid' => 'required']);

        if ($request->apikey != Config::get('cloud.brgApiKey')) {
            abort(401);
        }

        switch ($request->event) {
            case 'SNAPSHOT.CREATE':
                SnapshotMetadata::create(['id' => $request->uuid, 'ostypeid' => $request->ostypeid]);
                break;

            case 'SNAPSHOT.DELETE':
                SnapshotMetadata::destroy($request->uuid);
                break;
        }



        return response()->json(['status' => 'success']);
    }
}
