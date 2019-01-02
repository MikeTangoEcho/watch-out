<div class="stream">
    <span class="stream-title scroll-container">
        <p class="scroll-left">{{ $stream->user->name }}. {{ $stream->title }}</p>
    </span>
    <a class="stream-url material-icons" target="_blank" href="{{ route('streams.show', ['stream' => $stream->id])}}">launch</a>
    <i class="stream-mute material-icons">volume_off</i>
    <video
        poster="{{ asset('images/mire160x120.png') }}"
        class="streamer"
        muted
        autoplay
        data-src="{{ route('streams.pull', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}"
        data-mime-type="{{ $stream->mime_type }}">
    </video>
</div>
