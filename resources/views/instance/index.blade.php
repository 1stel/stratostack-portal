@extends('app')

@section('content')
    <div>
        @if(is_object($vms) !== true || property_exists($vms, "cserrorcode"))
            @if(property_exists($vms, "errortext"))
                Internal Error: {{ $vms->errortext }}
            @else
                Internal Error: Can't retrieve instances
            @endif
        @elseif(count($vms) > 0)
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>IP</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vms as $vm)
                    <tr>
                        <td><a href="{{ route('instance.show', $vm->id) }}">{{ $vm->name }}</a></td>
                        <td>{{ $vm->nic[0]->ipaddress }}</td>
                        <td>{{ $vm->templatedisplaytext }}</td>
                        <td><span class="label {{ ($vm->state == 'Running') ? 'label-success' : 'label-danger' }}">{{ $vm->state }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            I see you have no instances! Would you like to create one?
        @endif

        <div class="text-center"><a href="{{ route('instance.create') }}" class="btn btn-lg btn-primary">New Instance</a></div>
    </div>
@endsection
