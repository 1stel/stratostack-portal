@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="flash alert-danger">
            <strong>Ut oh.  We ran into a problem.</strong><br><br>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br/>
    @endif

    <h3>{{ $vm->name }} <small><span class="label {{ ($vm->state == 'Running') ? 'label-success' : 'label-danger' }}">{{ $vm->state }}</span></small></h3>
    <h4>{{ $vm->zonename }}</h4>

    <div class="row">
        <div class="col-md-2"><strong>{{ $vm->nic[0]->ipaddress }}</strong></div>
        <div class="col-md-5">
            <strong>
                {{ $vm->cpunumber }} {{ ngettext('Core', 'Cores', $vm->cpunumber) }} /
                {{ $vm->memory / 1024 }} Gb RAM /
                {{ $disk->size / 1024 / 1024 / 1024 }} Gb {{ $diskType->display_text }}
            </strong>
        </div>
        <div class="col-md-4"><strong>{{ $vm->templatedisplaytext }}</strong></div>
    </div>

    <div class="row" style="margin-top:35px">
        <div class="col-sm-2 instanceControl">
            <ul class="nav">
                @if ($vm->state != 'Destroyed')
                <li><a href="{{ ($vm->state == "Running" || $vm->state == "Starting") ? route('instance.stop', $vm->id) : route('instance.start', $vm->id) }}">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        Power {{ ($vm->state == "Running" || $vm->state == "Starting") ? 'Off' : 'On' }}
                    </a>
                </li>
                @if ($vm->state == "Running")
                    <li><a href="{{ route('instance.reboot', $vm->id) }}">
                            <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                            Reboot
                        </a>
                    </li>
                @endif
                <li><a href="{{ route('instance.snapshot', $vm->id) }}">
                        <span class="glyphicon glyphicon-camera" aria-hidden="true"></span>
                        Snapshot
                    </a>
                </li>

                @if ($vm->state == 'Stopped')
                <li><a href="{{ route('instance.resetpw', $vm->id) }}">
                        <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                         Reset Password
                    </a>
                </li>
                @endif

                <li><a href="{{ route('instance.reinstallvm', $vm->id) }}">
                        <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                         Reinstall
                    </a>
                </li>

                <li><a href="javascript:deleteType('instance', '{{ $vm->id }}');">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        Destroy
                    </a>
                </li>
                @else
                <li><a href="{{ route('instance.recover', $vm->id) }}"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Recover</a></li>
                @endif
            </ul>
        </div>

        @if ($vm->state == "Running")
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading"><h4 class="panel-title">Usage Metrics</h4></div>
                <table class="table">
                    <tr>
                        <th style="width: 5em">CPU</th>
                        <td>
                            @if (property_exists($vm, "cpuused"))
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="{{ round(substr($vm->cpuused, 0, -1)) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ round(substr($vm->cpuused, 0, -1)) }}%; min-width: 2em;">
                                    {{ round(substr($vm->cpuused, 0, -1)) }}%
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 5em">RAM</th>
                        <td>
                            @if (property_exists($vm, "memorykbs") && property_exists($vm, "memoryintfreekbs"))
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-valuenow="{{ ($vm->memorykbs - $vm->memoryintfreekbs) / 1024 }}" aria-valuemin="0" aria-valuemax="{{ $vm->memory }}" style="width: {{ 100 - round($vm->memoryintfreekbs / $vm->memorykbs * 100) }}%; min-width: 5em;">
                                    {{ round(($vm->memorykbs - $vm->memoryintfreekbs) / 1024) }} MB
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <div class="col-md-3">
            <div class="panel panel-info">
                <div class="panel-heading"><h4 class="panel-title">Snapshots</h4></div>
                <table class="table">
                    @foreach ($snapshots as $snapshot)
                        @if ($snapshot->volumeid == $disk->id)
                            <tr>
                                <td>{{ $snapshot->name }}</td>
                                <td>
                                    <a href="{{ route('snapshot.newTemplate', $snapshot->id) }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-open" aria-hidden="true"></span></a>
                                    <a href="javascript:deleteType('snapshot', '{{ $snapshot->id }}')" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-info">
                <div class="panel-heading"><h4 class="panel-title">Security Groups</h4></div>
                <div class="panel-body">
                    <ul class="list-group">
                        @foreach ($vm->securitygroup as $sg) <li class="list-group-item">{{ ucfirst($sg->name) }}</li> @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
$("#state").prop('disabled', true);

function deleteType(type, id) {
    if (confirm('Delete this ' + type +'?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/' + type + '/' + id, //resource
            success: function(jobid) {
                //if something was deleted, we redirect the user to the users page, and automatically the user that he deleted will disappear
                window.location = '/progress/' + jobid;
            }
        });
    }
}
@endsection