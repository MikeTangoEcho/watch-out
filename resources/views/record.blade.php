@extends('layouts.app')

@section('title', __('Record'))

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush

@section('content')
<div class="container recorder">
    <div class="row">
        <div class="col">
            <h1 class="float-right"><span class="views">0</span><i class="material-icons">person</i></h1>
            <stream-configuration inline-template id="update_form"
                default-title="{{ $stream->title }}"
                url="{{ route('streams.update', ['stream' => $stream->id]) }}">
                <div class="col-sm-6">
                    <form v-if="editing" @submit.prevent="updateStream">
                        <div class="form-group">
                            <div class="input-group is-invalid">
                                <input v-model="newTitle"
                                    type="text"
                                    :class="'form-control ' + (isInvalid('title') ? ' is-invalid' : '')"
                                    id="inputTitle"
                                    aria-describedby="titleHelp"
                                    placeholder="Enter Title">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success" type="submit" class="btn btn-sm">
                                        <i class="material-icons">edit</i>
                                    </button>
                                    <button class="btn btn-outline-danger" type="button" @click="cancelEdit()" class="btn btn-sm">
                                        <i class="material-icons">cancel</i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" v-if="isInvalid('title')">
                                    <ul class="list-unstyled">
                                        <li v-for="message in getErrors('title')">
                                            @{{ message }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                    <h1 v-else id="stream-title"  @click="editing = true">@{{ title }}</h1>
                </div>
            </stream-configuration>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="embed-responsive embed-responsive-16by9">
                <video id="preview"
                    muted
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
            <button id="stop" class="btn btn-secondary btn-block" type="button">{{ __('Stop') }}</button>
            <div id="success_message" class="alert alert-success">
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
    const recorderContainer = document.querySelector('div.recorder');
    const viewsCounter = document.querySelector('span.views');
    const streamTitle = document.querySelector('h1#stream-title');
    const inputTitle = document.querySelector('input#inputTitle');

    // Init Recorder
    var recorder = new Recorder({{ config('watchout.push_delay') }}, previewVideo, viewsCounter);

    // Event Listener
    recordButton.addEventListener('click', recorder.openStream.bind(recorder));
    recordButton.addEventListener('click', function (e) {
        recorderContainer.classList.toggle('recorder-recording');
    });
    stopButton.addEventListener('click', recorder.stopRecording.bind(recorder));
    stopButton.addEventListener('click', function (e) {
        recorderContainer.classList.toggle('recorder-recording');
        recorderContainer.classList.toggle('recorder-ended');
    });
</script>
@endpush