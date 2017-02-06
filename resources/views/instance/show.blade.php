@extends('app')

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
                {{ $disk->size / 1024 / 1024 / 1024 }} Gb
                @if(defined("diskType"))
                    {{ $diskType->display_text }}
                @else
                    Unknown Disk Type
                @endif
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

        <div class="col-md-10">
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