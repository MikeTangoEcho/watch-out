@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush


@section('content')
    <video controls width="460" height="306" autoplay>
        <source src="{{ route('stream.pull', ['ts' => now()->timestamp]) }}" type='video/webm'>
    </video>
@endsection