@extends('adminapp')

@section('content')
<div class="col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">Edit Template Group</div>
        <div class="panel-body">
            {!! Form::model($group, ['method' => 'PATCH', 'route' => ['admin.template.update', $group->id], 'class' => 'form-horizontal']) !!}

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
                        {!! Form::hidden('templateSize[' . $template->id . ']', ($template->size / 1024 / 1024 / 1024)) !!}
                        {!! Form::checkbox('templates[' . $template->id . ']', 1, (in_array($template->id, $checkedIDs)) ? true : false) !!} {{ $template->displaytext }} ({{ ($template->size / 1024 / 1024 / 1024) }} Gb)<br/>
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