@extends('app')

@section('content')
    <div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
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

                <table class="table">
                    <thead>
                        <tr>
                            <th>Key Name</th>
                            <th>Fingerprint</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach ($keys as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->fingerprint }}</td>
                        <td>
                            <a href="javascript:deleteKey('{{ $key->name }}');" class="btn btn-danger btn-sm">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                    </tbody>
                </table>

                <br/>
                <h1>New Key</h1>
                {!! Form::open(['method' => 'POST', 'route' => 'settings.sshkeys.store']) !!}

                <!-- Name Form Input -->
                <div class="form-group">
                    {!! Form::label('name', 'Name:') !!}
                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                </div>

                <!-- Public Key Form Input -->
                <div class="form-group">
                    {!! Form::label('publicKey', 'Public Key:') !!}
                    {!! Form::textarea('publicKey', null, ['class' => 'form-control']) !!}
                </div>

                {!! Form::submit('Add Key', ['class' => 'btn btn-primary']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('js')
function deleteKey(id) {
    if (confirm('Delete this SSH Key?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/settings/sshkeys/' + id, //resource
            success: function() {
                location.reload();
            }
        });
    }
}
@endsection