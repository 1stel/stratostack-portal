@extends('adminapp')

@section('content')
<div class="col-sm-8 col-sm-offset-2">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">Setup</div>
        <div class="panel-body">
            {!! Form::open(['route' => 'admin.setup.save', 'method' => 'POST', 'class' => 'form-horizontal']) !!}

            <!-- Url Form Input -->
            <div class="form-group">
                {!! Form::label('recordsUrl', 'Records Server URL', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-7">
                    {!! Form::text('recordsUrl', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- DomainId Form Input -->
            <div class="form-group">
                {!! Form::label('domainId', 'Domain ID', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-7">
                    {!! Form::text('domainId', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            
            <!-- Form Submit -->
            <div class="form-group">
                <div class="col-sm-7 col-sm-offset-3">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection