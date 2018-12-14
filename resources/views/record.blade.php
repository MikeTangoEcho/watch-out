@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush

@section('content')
<div class="container">
    <div class="embed-responsive embed-responsive-16by9">
        <video id="preview" muted autoplay playsinline></video>
    </div>
    <button id="record" class="btn btn-danger" type="button">Record</button>
    <button id="stop" class="btn btn-secondary" type="button">Stop</button>
    <button id="download" class="btn btn-success" type="button">DDL</button>
</div>
@endsection