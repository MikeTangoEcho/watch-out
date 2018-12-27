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
                    width="640"
                    height="480"
                    autoplay
                    playsinline
                    data-src="{{ route('streams.push', ['stream' => $stream->id])}}"
                    data-mime-type="{{ $stream->mime_type }}"></video>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button id="record" class="btn btn-danger btn-block" type="button">Record</button>
            <button id="stop" class="btn btn-secondary btn-block invisible" type="button">Stop</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const previewVideo = document.querySelector('video#preview');
    const recordButton = document.querySelector('button#record');
    const stopButton = document.querySelector('button#stop');

    // Init Recorder
    var recorder = new Recorder(previewVideo);

    // Event Listener
    recordButton.addEventListener('click', recorder.openStream.bind(recorder));
    recordButton.addEventListener('click', function (e) {
    this.classList.toggle('invisible');
    stopButton.classList.toggle('invisible');
    });
    stopButton.addEventListener('click', recorder.stopRecording.bind(recorder));
</script>
@endpush