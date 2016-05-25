@extends('adminapp')

@section('content')
<table class="table">
    <thead>
    <tr>
        <th>Template Group Name</th>
        <th>Type</th>
        <th>Template Count</th>
        <th><a href="{{ route('admin.template.create') }}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($groups as $group)
        <tr>
            <td>{{ $group->name }}</td>
            <td>{{ $group->type }}</td>
            <td>{{ $group->templates->count() }}</td>
            <td>
                <a href="{{ route('admin.template.edit', $group->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                <a href="javascript:deleteTemplate({{ $group->id }})" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

@section('js')
function deleteTemplate(id) {
    if (confirm('Delete this template group?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/admin/template/' + id, //resource
            success: function(affectedRows) {
                //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                if (affectedRows > 0) window.location = '/admin/template/';
            }
        });
    }
}
@endsection