@extends('app')

@section('content')
<div class="col-md-6 col-md-offset-3">
    <h3>Domains</h3>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th class="text-right"><a href="{{ route('dns.create') }}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($domains as $domain)
            <tr>
                <td>{{ $domain->name }}</td>
                <td class="text-right">
                    <a href="{{ route('dns.edit', $domain->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a href="javascript:deleteDomain({{ $domain->id }})" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection

@section('js')
function deleteDomain(id) {
    if (confirm('Delete this domain?  This will also delete all domain records.')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/dns/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/dns/';
            }
        });
    }
}
@endsection