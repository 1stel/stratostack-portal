@extends('app')

@section('content')

    <div class="col-md-10 col-md-offset-1" style="margin-top: 30px">
        <div class="row">
            <div class="col-md-6" style="font-size: 18px">
                <strong>{{ Config::get('app.name') }}</strong><br>
                Address1<br>
                City, State ZIP<br>
            </div>
            <div class="col-md-6 text-right" style="font-size: 18px">
                Invoice: {{ $invoiceData['invoiceNumber'] }}<br>
                Invoice Date: {{ $date->toDatestring() }}<br>
                Period: (period)<br>
            </div>
        </div>

        <table class="table" style="margin-top:25px">
            <tr>
                <th>Instance</th>
                <th>Resources</th>
                <th>Usage</th>
                <th>Start</th>
                <th>End</th>
                <th>Price</th>
            </tr>
            @foreach ($invoiceData['instance'] as $instanceId =>$instance)
                @foreach ($instance as $resources)
                    <tr>
                        <td>{{ $instanceId }}</td>
                        <td>
                            {{ $resources['resources']['cpunumber'] }} Core /
                            {{ $resources['resources']['memory'] / 1024 }} Gb RAM /
                            {{ $resources['resources']['disk_size'] / 1024 / 1024 / 1024 }} Gb</td>
                        <td>{{ round($resources['usage'], 2) }}</td>
                        <td>{{ date('M j G:s', strtotime($resources['startdate']))  }}</td>
                        <td>{{ date('M j G:s', strtotime($resources['enddate'])) }}</td>
                        <td>${{ round($resources['price'], 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </table>
        <p class="text-right"><strong>Total: ${{ round($invoiceData['total'], 2) }}</strong></p>
    </div>
@endsection