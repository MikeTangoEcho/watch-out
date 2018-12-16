@extends('layout.app')


@section('content')
    <video controls width="460" height="306" autoplay>
        <source src="{{ route('stream.pull', ['ts' => now()->timestamp]) }}" type='video/webm'>
    </video>
@endsection