@extends('adminapp')

@section('content')
    <div class="col-md-6 col-md-offset-3">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right"><a href="{{ route('admin.settings.edit') }}"><span class="glyphicon glyphicon-pencil"></span></a></div>
                <h4 class="panel-title">Records Server</h4>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Records Server URL</dt>
                    <dd>{{ $settings['recordsUrl'] }}</dd>

                    <dt>Domain ID</dt>
                    <dd>{{ $settings['domainId'] }}</dd>

                    <dt>Hypervisor</dt>
                    <dd>{{ $settings['hypervisor'] }}</dd>

                    <dt>Root Disk Resize</dt>
                    <dd>{{ $settings['rootdiskresize'] }}</dd>
                </dl>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right"><a href="{{ route('admin.settings.edit') }}"><span class="glyphicon glyphicon-pencil"></span></a></div>
                <h4 class="panel-title">Billing</h4>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Credit Limit</dt>
                    <dd>${{ $settings['creditLimit'] }}</dd>

                    <dt>Default Payment Type</dt>
                    <dd>PostPay</dd>

                    <dt>Vouchers Issued</dt>
                    <dd>{{ $settings['vouchers'] }}</dd>

                    <dt>Voucher Amount</dt>
                    <dd>${{ $settings['voucherAmount'] }}</dd>

                    <dt>Hours in a Month</dt>
                    <dd>{{ $settings['hoursInMonth'] }}</dd>

                    <dt>Grandfather Pricing</dt>
                    <dd>{{ $settings['grandfatherPricing'] }}</dd>
                </dl>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right"><a href="{{ route('admin.settings.edit') }}"><span class="glyphicon glyphicon-pencil"></span></a></div>
                <h4 class="panel-title">DNS</h4>
            </div>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>DNS Server API</dt>
                    <dd>{{ $settings['dnsServer'] }}</dd>

                    <dt>API Key</dt>
                    <dd>{{ $settings['dnsApiKey'] }}</dd>
                </dl>
            </div>
        </div>

        <a href="{{ route('admin.settings.updateCloud') }}" class="btn btn-primary">Update Cloud Settings</a>
    </div>

@endsection