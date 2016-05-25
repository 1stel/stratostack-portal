@extends('app')

@section('content')
<h3>Last 30 Days Activity</h3>
    <div class="col-md-8 col-md-offset-2">
        <table class="table">
            <thead>
            <tr>
                <th>Activity</th>
                <th>IP Address</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($events->sortByDesc('created_at') as $event)
                <tr>
                    <td>{{ $event->subject_type }} {{ $event->event }}</td>
                    <td>{{ $event->ip }}</td>
                    <td>{{ $event->created_at->toFormattedDateString() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection