@extends('adminapp')

@section('content')
<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th><a href="{{ route('admin.user.create') }}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                <a href="javascript:deleteUser({{ $user->id }})" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

@section('js')
function deleteUser(id) {
    if (confirm('Delete this user?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/admin/user/' + id, //resource
            success: function(affectedRows) {
                //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                if (affectedRows > 0) window.location = '/admin/user/';
            }
        });
    }
}
@endsection