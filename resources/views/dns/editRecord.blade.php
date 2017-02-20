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
        <div class="panel-heading">Edit DNS Record</div>
        <div class="panel-body">
            {!! Form::model($record, ['route' => ['dnsRecord.update', $record->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}

            @if (!empty($record->name))
            <!-- Name Form Input -->
            <div class="form-group">
                {!! Form::label('name', 'Name:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-5">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @endif

            <!-- Type Form Input -->
            <div class="form-group">
                {!! Form::label('type', 'Type', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('type', null, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>

            <!-- Target Form Input -->
            <div class="form-group">
                {!! Form::label('target', 'Target:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-5">
                    {!! Form::text('target', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            @if (!empty($record->priority))
            <!-- Priority Form Input -->
            <div class="form-group">
                {!! Form::label('priority', 'Priority:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('priority', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @endif

            @if (!empty($record->port))
            <!-- Port Form Input -->
            <div class="form-group">
                {!! Form::label('port', 'Port:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('port', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @endif

            @if (!empty($record->weight))
            <!-- Weight Form Input -->
            <div class="form-group">
                {!! Form::label('weight', 'Weight:', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            @endif

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