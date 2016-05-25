@extends('adminapp')

@section('content')
<div class="container">
    Networks<br/>

    @foreach ($networks as $network)
    {{ $network->name }} {{ $network->displaytext }} ({{ $network->id }}) - {{ $network->canusefordeploy }}<br/>
    @endforeach
</div>
@endsection