@extends('adminapp')

@section('content')
<div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">New Template Group</div>
        <div class="panel-body">
            {!! Form::open(['method' => 'POST', 'route' => 'admin.template.store', 'class' => 'form-horizontal', 'files' => true]) !!}

            <!-- Name Form Input -->
            <div class="form-group">
                {!! Form::label('name', 'Name', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('type', 'Type', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-2">
                    {!! Form::select('type', ['IaaS' => 'IaaS', 'SaaS' => 'SaaS'], null, ['class' => 'form-control']) !!}
                </div>
            </div>

            <!-- Display_img Form Input -->
            <div class="form-group">
                {!! Form::label('display_img', 'Display Image', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::file('display_img') !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('templates', 'Templates', ['class' => 'col-sm-3 control-label']) !!} <br/>
                <div class="col-sm-6">
                    @foreach ($templates as $template)
                        {!! Form::hidden('templates[' . $template->id . ']', '0') !!}
                        @if(property_exists($template, "size"))
                            {!! Form::hidden('templateSize[' . $template->id . ']', ($template->size / 1024 / 1024 / 1024)) !!}
                        @endif
                        {!! Form::checkbox('templates[' . $template->id . ']') !!} {{ $template->displaytext }} 
                        @if(property_exists($template, "size"))
                            ({{ ($template->size / 1024 / 1024 / 1024) }} Gb)
                        @endif<br/>
                    @endforeach
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