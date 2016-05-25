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
        <div class="panel-heading">Change Password</div>
        <div class="panel-body">
            {!! Form::open(['method' => 'PATCH', 'route' => 'admin.profile.update', 'class' => 'form-horizontal', 'files' => true]) !!}

            @include('admin.profile.form')

            {!! Form::close() !!}
        </div>
    </div>
</div>



@endsection