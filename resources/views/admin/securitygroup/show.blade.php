@extends('adminapp')

@section('content')
    <div class="col-sm-8 col-sm-offset-2">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif

        <div class="panel panel-primary">
            <div class="panel-heading"><h4 class="panel-title">Security Group: {{ $group->name }}</h4></div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>CIDR</th>
                    <th>Protocol</th>
                    <th>ICMP Type / Code</th>
                    <th>Start - End Ports</th>
                    <th></th>
                </tr>
                <tr>
                    {!! Form::open(['route' => ['admin.sg.ingressrule.add', $group->id], 'method' => 'POST', 'class' => 'form-horizontal']) !!}
                    <td>{!! Form::text('cidr', null, ['class' => 'form-control']) !!}</td>
                    <td>{!! Form::select('protocol', ['TCP' => 'TCP', 'UDP' => 'UDP', 'ICMP' => 'ICMP'], null, ['class' => 'form-control']) !!}</td>
                    <td style="text-align: center">
                        {!! Form::text('icmpType', null, ['class' => 'form-control', 'style' => 'width:3em; display:inline']) !!} /
                        {!! Form::text('icmpCode', null, ['class' => 'form-control', 'style' => 'width:3em; display:inline']) !!}
                    </td>
                    <td style="text-align: center">
                        {!! Form::text('startPort', null, ['class' => 'form-control', 'style' => 'width:5em; display:inline']) !!} -
                        {!! Form::text('endPort', null, ['class' => 'form-control', 'style' => 'width:5em; display:inline']) !!}
                    </td>
                    <td>{!! Form::button('<span class="glyphicon glyphicon-plus"></span>', ['type' => 'submit', 'class' => 'btn btn-sm btn-success']) !!}</td>
                    {!! Form::close() !!}
                </tr>
                </thead>
                <tbody>
                @foreach ($group->ingressRules as $rule)
                    <tr>
                        <td>{{ $rule->cidr }}</td>
                        <td>{{ $rule->protocol }}</td>
                        <td style="text-align: center">@if ($rule->protocol == 'ICMP') {{ $rule->icmp_type }} / {{ $rule->icmp_code }} @endif</td>
                        <td style="text-align: center">@if ($rule->protocol != 'ICMP') {{ $rule->start_port }} - {{ $rule->end_port }} @endif</td>
                        <td>
                            <a href="javascript:deleteElement({{ $rule->id }})" class="btn btn-sm btn-danger">
                                <span class="glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
function deleteElement(id) {
    if (confirm('Delete this package?')) {
        $.ajax({
            type: "DELETE",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            url: '/admin/sg/ingressrule/' + id,
            success: function(affectedRows) {
                if (affectedRows > 0) window.location = '/admin/sg/';
            }
        });
    }
}
@endsection