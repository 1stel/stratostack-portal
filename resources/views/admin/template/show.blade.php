@extends('adminapp')

@section('content')
    Template Group - {{ $group->name }} - {{ $group->type }}<br/><br/>

    @foreach ($templates as $template)
        <li>{{ $template->size }} - {{ $template->price }}</li>
    @endforeach
@endsection