@extends('layouts.app')

@section('content')
<div class="col-md-8 col-md-offset-2">
    @if ($errors->any())
        <div class="flash alert-danger">
            <strong>There were some problems with your input.</strong><br><br>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br/>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">New Domain</div>
        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => 'dns.store', 'class' => 'form-horizontal', 'files' => true]) !!}

            <!-- Name Form Input -->
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Form Submit -->
            <div class="form-group">
                <div class="col-sm-6 col-sm-offset-3">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
                </div>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection