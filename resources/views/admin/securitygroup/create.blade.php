@extends('adminapp')

@section('content')
    <div class="col-sm-6 col-sm-offset-3">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        <div class="panel panel-primary">
            <div class="panel-heading"><h4 class="panel-title">New Security Group</h4></div>
            <div class="panel-body">
                {!! Form::open(['route' => 'admin.sg.store', 'method' => 'POST']) !!}

                <!-- Name Form Input -->
                <div class="form-group">
                    {!! Form::label('name', 'Name') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>

                <!-- Description Form Input -->
                <div class="form-group">
                    {!! Form::label('description', 'Description') !!}
                    {!! Form::text('description', null, ['class' => 'form-control']) !!}
                </div>

                <!-- Form Submit -->
                {!! Form::submit('Add', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </div>
@endsection