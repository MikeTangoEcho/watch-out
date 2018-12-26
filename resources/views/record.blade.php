@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush

@section('content')
<h1>{{ $stream->id }} - {{ $stream->title }}</h1>
<div class="container">
    <div class="embed-responsive embed-responsive-16by9">
        <video id="preview"
            muted
            autoplay
            playsinline
            data-src="{{ route('stream.push', ['stream' => $stream->id])}}"
            data-mime-type="{{ $stream->mime_type }}"></video>
    </div>
    <button id="record" class="btn btn-danger" type="button">Record</button>
    <button id="stop" class="btn btn-secondary" type="button">Stop</button>
</div>
@endsection