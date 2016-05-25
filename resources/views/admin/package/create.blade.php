@extends('adminapp')

@section('content')
<div class="col-md-8 col-md-offset-2">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading"><h4 class="panel-title">New Package</h4></div>
        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => 'admin.package.store', 'class' => 'form-horizontal']) !!}

            <!-- Cpu_number Form Input -->
            <div class="form-group">
                {!! Form::label('cpu_number', '# of Cores', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::selectRange('cpu_number', 1, $cpuLimit, null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Ram Form Input -->
            <div class="form-group">
                {!! Form::label('ram', 'RAM', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    <div class="input-group">
                        {!! Form::select('ram', $ramLimit, null, ['class' => 'form-control']) !!}
                        <div class="input-group-addon">GB</div>
                    </div>
                </div>
            </div>

            <!-- Disk_type Form Input -->
            <div class="form-group">
                {!! Form::label('disk_type', 'Disk Type', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('disk_type', $disks, null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Disk_size Form Input -->
            <div class="form-group">
                {!! Form::label('disk_size', 'Disk Size', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    <div class="input-group">
                        {!! Form::text('disk_size', null, ['class' => 'form-control']) !!}
                        <div class="input-group-addon">GB</div>
                    </div>
                </div>
            </div>

            <!-- Price Form Input -->
            <div class="form-group">
                {!! Form::label('price', 'Price', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-4">
                    <div class="input-group">
                        <div class="input-group-addon">$</div>
                        {!! Form::text('price', null, ['class' => 'form-control']) !!}
                        <div class="input-group-addon">per Month</div>
                    </div>
                </div>
            </div>

            <!-- Tic Form Input -->
            <div class="form-group">
                {!! Form::label('tic', 'TIC', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-4">
                    {!! Form::text('tic', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- PaymentTypeOverride Form Input -->
            <div class="form-group">
                {!! Form::label('paymentTypeOverride', 'Payment Type Override', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('paymentTypeOverride', ['PostPay', 'PrePay'], null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Form Submit -->
            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-4">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection