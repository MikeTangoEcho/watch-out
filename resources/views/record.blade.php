@extends('layouts.app')

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1>{{ $stream->title }}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="embed-responsive embed-responsive-16by9">
                <video id="preview"
                    muted
                    width="320"
                    height="240"
                    autoplay
                    playsinline
                    data-src="{{ route('streams.push', ['stream' => $stream->id])}}"
                    data-mime-type="{{ $stream->mime_type }}"></video>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button id="record" class="btn btn-danger btn-block" type="button">{{ __('Record') }}</button>
            <button id="stop" class="btn btn-secondary btn-block invisible" type="button">{{ __('Stop') }}</button>
            <div id="success_message" class="alert alert-success invisible">
                <h2>{{ __('Thank you for streaming') }}</h2>
                <a class="btn btn-success" href="{{ route('streams.record') }}">{{ __('Restart Stream') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const previewVideo = document.querySelector('video#preview');
    const recordButton = document.querySelector('button#record');
    const stopButton = document.querySelector('button#stop');
    const successMessage = document.querySelector('div#success_message');

    // Init Recorder
    var recorder = new Recorder(previewVideo);

    // Event Listener
    recordButton.addEventListener('click', recorder.openStream.bind(recorder));
    recordButton.addEventListener('click', function (e) {
        this.classList.toggle('invisible');
        stopButton.classList.toggle('invisible');
    });
    stopButton.addEventListener('click', recorder.stopRecording.bind(recorder));
    stopButton.addEventListener('click', function (e) {
        this.classList.toggle('invisible');
        successMessage.classList.toggle('invisible');
    });
</script>
@endpush