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
        <div class="panel-heading">Edit Zone</div>
        <div class="panel-body">
            {!! Form::model($zone, ['route' => ['admin.zone.update', $zone->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}

            <!-- Name Form Input -->
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Display_name Form Input -->
            <div class="form-group">
                {!! Form::label('display_name', 'Display Name', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Status Select -->
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-3">
                    {!! Form::select('status', ['Disabled' => 'Disabled', 'Enabled' => 'Enabled'], null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Form Submit -->
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-3">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection