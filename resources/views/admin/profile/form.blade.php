<!-- Password Form Input -->
<div class="form-group">
    {!! Form::label('password', 'Password', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::password('password', ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Password_confirm Form Input -->
<div class="form-group">
    {!! Form::label('password_confirmation', 'Confirm', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-6">
        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
    </div>
</div>

<!-- Form Submit -->
<div class="form-group">
    <div class="col-sm-6 col-sm-offset-3">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
        {!! Form::reset('Reset', ['class' => 'btn btn-default']) !!}
    </div>
</div>