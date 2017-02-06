<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'Auth\LoginController@getLogin');

Route::get('home', 'InstanceController@index');

Route::get('/emailVerification/{token}', function ($token) {
    $user = \App\User::where('email_token', $token)->first();

    if ($user instanceof \App\User) {
        $user->email_token = null;
        $user->verified = 1;
        $user->bill_date = date('Y-m-d', strtotime("+1 month"));
        $user->save();

        event(new \App\Events\UserWasCreated($user));

        flash()->success('Thank you for verifying your email address.');
        return redirect()->route('instance.index');
    }

    return response('Error', 404);
});

Route::get('/jobStatus/{jobId}', ['as' => 'jobStatus', 'middleware' => 'auth', function (\Cloudstack\CloudStackClient $acs, $jobId) {
    $result = $acs->queryAsyncJobResult(['jobid' => $jobId]);
    return response()->json($result);
}]);

Route::get('/progress/{jobId}', ['as' => 'progress', 'middleware' => 'auth', function ($jobId) {
    return view('progress', ['jobId' => $jobId]);
}]);

Auth::routes();

Route::resource('instance', 'InstanceController');
Route::resource('dns', 'DNSController', ['except' => ['show', 'update']]);
Route::post('dnsRecord', ['as' => 'dnsRecord.store', 'uses' => 'DNSController@storeRecord']);
Route::get('dnsRecord/{record}/edit', ['as' => 'dnsRecord.edit', 'uses' => 'DNSController@editRecord']);
Route::put('dnsRecord/{record}', ['as' => 'dnsRecord.update', 'uses' => 'DNSController@updateRecord']);
Route::patch('dnsRecord/{record}', 'DNSController@updateRecord');
Route::delete('dnsRecord/{record}', ['as' => 'dnsRecord.destroy', 'uses' => 'DNSController@destroyRecord']);

Route::get('instance/{instance}/start', ['as' => 'instance.start', 'uses' => 'InstanceController@start']);
Route::get('instance/{instance}/reboot', ['as' => 'instance.reboot', 'uses' => 'InstanceController@reboot']);
Route::get('instance/{instance}/stop', ['as' => 'instance.stop', 'uses' => 'InstanceController@stop']);
Route::get('instance/{instance}/restore', ['as' => 'instance.recover', 'uses' => 'InstanceController@recover']);
Route::get('instance/{instance}/snapshot', ['as' => 'instance.snapshot', 'uses' => 'InstanceController@snapshot']);

Route::get('snapshot/{snapshot}/createTemplate', ['as' => 'snapshot.newTemplate', 'uses' => 'InstanceController@newTemplateFromSnapshot']);
Route::post('snapshot/{snapshot}/createTemplate', ['as' => 'snapshot.createTemplate', 'uses' => 'InstanceController@createTemplateFromSnapshot']);
Route::get('snapshot/{snapshot}', ['as' => 'snapshot.deploy', 'uses' => 'InstanceController@deploySnapshot']);
Route::delete('snapshot/{snapshot}', ['as' => 'snapshot.destroy', 'uses' => 'InstanceController@deleteSnapshot']);

// Settings Section
Route::get('settings', ['as' => 'settings.profile', 'uses' => 'Settings\ProfileController@profile']);
Route::put('settings/profile', ['as' => 'settings.profile.update', 'uses' => 'Settings\ProfileController@update']);
Route::resource('settings/vouchers', 'Settings\VouchersController', ['only' => ['index', 'update']]);
Route::post('settings/vouchers/redeem', ['as' => 'settings.vouchers.redeem', 'uses' => 'Settings\VouchersController@redeem']);
Route::get('settings/security', ['as' => 'settings.security', 'uses' => 'Settings\SecurityController@index']);
Route::get('settings/billing', ['as' => 'settings.billing', 'uses' => 'Settings\BillingController@billing']);
Route::get('settings/billing/invoice/{id}', ['as' => 'invoice.show', 'uses' => 'Settings\BillingController@getInvoice']);
Route::resource('settings/sshkeys', 'Settings\SSHKeyController', ['only' => ['store', 'destroy']]);
Route::resource('settings/creditcard', 'Settings\CreditCardController', ['except' => ['index', 'show']]);
Route::get('settings/activity', ['as' => 'settings.activity', 'uses' => 'Settings\ActivityController@activity']);

// Admin routes
Route::get('admin/', ['as' => 'admin.home', 'uses' => 'Admin\HomeController@index']);
Route::resource('admin/template', 'Admin\TemplateController', ['except' => ['show']]);
Route::resource('admin/package', 'Admin\PackageController', ['except' => ['show', 'edit', 'update']]);
Route::resource('admin/network', 'Admin\NetworkController');
Route::resource('admin/user', 'Admin\UserController', ['except' => 'show']);
Route::resource('admin/sg', 'Admin\SecurityGroupController', ['except' => ['edit', 'update']]);
Route::post('admin/sg/{id}/ingressrule', ['as' => 'admin.sg.ingressrule.add', 'uses' => 'Admin\SecurityGroupController@addIngressRule']);
Route::delete('admin/sg/ingressrule/{id}', ['as' => 'admin.sg.ingressrule.destroy', 'uses' => 'Admin\SecurityGroupController@deleteIngressRule']);

Route::get('admin/zone', ['as' => 'admin.zone.index', 'uses' => 'Admin\ZoneController@index']);
Route::get('admin/zone/{zone}/edit', ['as' => 'admin.zone.edit', 'uses' => 'Admin\ZoneController@edit']);
Route::put('admin/zone/{zone}', ['as' => 'admin.zone.update', 'uses' => 'Admin\ZoneController@update']);
Route::patch('admin/zone/{zone}', 'Admin\ZoneController@update');
Route::get('admin/settings', ['as' => 'admin.settings.index', 'uses' => 'Admin\SettingsController@index']);
Route::get('admin/settings/edit', ['as' => 'admin.settings.edit', 'uses' => 'Admin\SettingsController@edit']);
Route::post('admin/settings', ['as' => 'admin.settings.save', 'uses' => 'Admin\SettingsController@save']);
Route::get('admin/settings/updateCloud', ['as' => 'admin.settings.updateCloud', 'uses' => 'Admin\SettingsController@updateCloud']);
Route::get('admin/setup', ['as' => 'admin.setup.index', 'uses' => 'Admin\SetupController@index']);
Route::post('admin/setup', ['as' => 'admin.setup.save', 'uses' => 'Admin\SetupController@save']);
Route::get('admin/profile', ['as' => 'admin.profile.edit', 'uses' => 'Admin\ProfileController@edit']);
Route::patch('admin/profile', ['as' => 'admin.profile.update', 'uses' => 'Admin\ProfileController@update']);
