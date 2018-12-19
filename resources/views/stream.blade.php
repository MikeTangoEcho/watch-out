@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush


@section('content')
    <video id="player" controls width="460" height="306" data-src="{{ route('stream.pull', ['ts' => now()->timestamp]) }}">
    </video>

    <video id="test" controls width="460" height="306" src="{{ route('stream.full', ['ts' => now()->timestamp]) }}">
    </video>
@endsection