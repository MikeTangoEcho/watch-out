@extends('layout.app')


@section('content')
    <video width="460" height="306" autoplay>
        <source src="{{ route('stream.stream', ['ts', now()]) }}">
    </video>
@endsection