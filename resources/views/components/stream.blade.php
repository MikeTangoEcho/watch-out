<h1>{{ $stream->user->name }}. {{ $stream->title }}</h1>
<div class="embed-responsive embed-responsive-16by9">
    <video
        poster="{{ asset('images/mire160x120.png') }}"
        class="streamer"
        controls
        muted
        autoplay
        data-src="{{ route('streams.pull', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}"
        data-mime-type="{{ $stream->mime_type }}">
    </video>
</div>
