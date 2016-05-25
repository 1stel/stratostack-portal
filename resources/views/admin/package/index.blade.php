@extends('adminapp')

@section('content')
<table class="table">
    <thead>
    <tr>
        <th>Cores</th>
        <th>RAM</th>
        <th>Disk</th>
        <th>Price</th>
        <th><a href="{{ route('admin.package.create') }}" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($packages as $package)
        <tr>
            <td>{{ $package->cpu_number }}</td>
            <td>{{ $package->ram / 1024 }} GB</td>
            <td>{{ $package->disk_size }} GB {{ $package->diskType->display_text }}</td>
            <td>${{ $package->price }}</td>
            <td>
                <a href="javascript:deletePackage({{ $package->id }})" class="btn btn-danger btn-sm">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection

@section('js')
function deletePackage(id) {
    if (confirm('Delete this package?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/admin/package/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/admin/package/';
            }
        });
    }
}
@endsection