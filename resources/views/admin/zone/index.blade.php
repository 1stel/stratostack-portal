@extends('adminapp')

@section('content')
<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>Display Name</th>
        <th>Status</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($zones as $zone)
        <tr>
            <td>{{ $zone->name }}</td>
            <td>{{ $zone->display_name }}</td>
            <td>{{ $zone->status }}</td>
            <td><a href="{{ route('admin.zone.edit', $zone->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
