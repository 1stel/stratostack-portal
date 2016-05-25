@extends('app')

@section('content')
    <div class="">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">

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
                
                {!! Form::open(['route' => ['settings.profile.update'], 'method' => 'PUT']) !!}

                <!-- Name Form Input -->
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', $user->name, ['class' => 'form-control']) !!}
                </div>

                <!-- Email Form Input -->
                <div class="form-group">
                    {!! Form::label('email', 'Email:') !!}
                    {!! Form::text('email', $user->email, ['class' => 'form-control']) !!}
                </div>

                <!-- Password Form Input -->
                <div class="form-group">
                    {!! Form::label('password', 'Password:') !!}
                    {!! Form::text('password', null, ['class' => 'form-control']) !!}
                </div>

                <!-- New_password Form Input -->
                <div class="form-group">
                    {!! Form::label('new_password', 'New Password:') !!}
                    {!! Form::text('new_password', null, ['class' => 'form-control']) !!}
                </div>

                <!-- New_password_confirm Form Input -->
                <div class="form-group">
                    {!! Form::label('new_password_confirmation', 'Confirm:') !!}
                    {!! Form::text('new_password_confirmation', null, ['class' => 'form-control']) !!}
                </div>
                
                <!-- Form Submit -->
                {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection