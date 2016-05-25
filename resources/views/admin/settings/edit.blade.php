@extends('adminapp')

@section('content')
    <div class="col-md-10 col-md-offset-1">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        {!! Form::open(['route' => 'admin.settings.save', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Records Server</h3></div>
            <div class="panel-body">
                <!-- Url Form Input -->
                <div class="form-group">
                    {!! Form::label('recordsUrl', 'Records Server URL', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-7">
                        {!! Form::text('recordsUrl', $settings['recordsUrl'], ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- Domain_id Form Input -->
                <div class="form-group">
                    {!! Form::label('domainId', 'Domain ID', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-7">
                        {!! Form::text('domainId', $settings['domainId'], ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- Hypervisor Form Input -->
                <div class="form-group">
                    {!! Form::label('hypervisor', 'Hypervisor', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('hypervisor',
                        ['HyperV' => 'HyperV', 'KVM' => 'KVM', 'XenServer' => 'XenServer', 'VMWare' => 'VMWare', 'LXC' => 'LXC'],
                        $settings['hypervisor'],
                        ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- RootDiskResize Form Input -->
                <div class="form-group">
                    {!! Form::label('rootdiskresize', 'Root Disk Resize', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('rootdiskresize', ['TRUE' => 'Yes', 'FALSE' => 'No'], $settings['rootdiskresize'], ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">Billing</h3></div>
            <div class="panel-body">
                <!-- CreditLimit Form Input -->
                <div class="form-group">
                    {!! Form::label('creditLimit', 'Credit Limit', ['class' => 'col-sm-3 col-sm-offset-2 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('creditLimit', $settings['creditLimit'], ['class' => 'form-control']) !!}
                    </div>

                </div>

                <!-- Default Payment Type Form Input -->
                <div class="form-group">
                    {!! Form::label('defaultPaymentType', 'Default Payment Type', ['class' => 'col-sm-3 col-sm-offset-2  control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('defaultPaymentType', ['PostPay' => 'PostPay', 'PrePay' => 'PrePay'], null, ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- Vouchers Form Input -->
                <div class="form-group">
                    {!! Form::label('vouchers', 'Vouchers', ['class' => 'col-sm-3 col-sm-offset-2  control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('vouchers', $settings['vouchers'], ['class' => 'form-control']) !!}
                    </div>

                </div>

                <!-- Voucher_amount Form Input -->
                <div class="form-group">
                    {!! Form::label('voucherAmount', 'Voucher Amount', ['class' => 'col-sm-3 col-sm-offset-2  control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('voucherAmount', $settings['voucherAmount'], ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- HoursInMonth Form Input -->
                <div class="form-group">
                    {!! Form::label('hoursInMonth', 'Hours In a Month', ['class' => 'col-sm-3 col-sm-offset-2  control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('hoursInMonth', $settings['hoursInMonth'], ['class' => 'form-control']) !!}
                    </div>
                </div>

                <!-- GrandfatherPricing Form Input -->
                <div class="form-group">
                    {!! Form::label('grandfatherPricing', 'Grandfather Pricing', ['class' => 'col-sm-3 col-sm-offset-2 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::select('grandfatherPricing', ['YES' => 'Yes', 'NO' => 'No'], $settings['grandfatherPricing'], ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">DNS</h3></div>
            <div class="panel-body">
                <!-- CreditLimit Form Input -->
                <div class="form-group">
                    {!! Form::label('dnsServer', 'DNS Server API', ['class' => 'col-sm-3 col-sm-offset-2 control-label']) !!}
                    <div class="col-sm-3">
                        {!! Form::text('dnsServer', $settings['dnsServer'], ['class' => 'form-control']) !!}
                    </div>

                </div>

                <!-- Default Payment Type Form Input -->
                <div class="form-group">
                    {!! Form::label('dnsApiKey', 'API Key', ['class' => 'col-sm-3 col-sm-offset-2  control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('dnsApiKey', $settings['dnsApiKey'], ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-5">
                <!-- Form Submit -->
                {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}

                <!-- Form Reset -->
                {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

@endsection