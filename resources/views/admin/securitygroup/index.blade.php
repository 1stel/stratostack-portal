@extends('adminapp')

@section('content')
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading"><h4 class="panel-title">Security Groups</h4></div>
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Ingress Rules</th>
                    <th style="width:90px">
                        <a href="{{ route('admin.sg.create') }}" class="btn btn-sm btn-success">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @if ($groups->count() > 0)
                    @foreach ($groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->description }}</td>
                            <td>{{ $group->ingressRules->count() }}</td>
                            <td>
                                <a href="{{ route('admin.sg.show', $group->id) }}" class="btn btn-sm btn-primary">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                                <a href="javascript:deleteElement({{ $group->id }})" class="btn btn-sm btn-danger">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">There are no security groups.</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
function deleteElement(id) {
    if (confirm('Delete this security group?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/admin/sg/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/admin/sg/';
            }
        });
    }
}
@endsection