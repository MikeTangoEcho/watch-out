<h4>{{ $stream->id }} - {{ $stream->title }}</h4>
<video
    class="streamer"
    muted
    width="240"
    height="180"
    autoplay
    data-src="{{ route('stream.pull', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}"
    data-mime-type="{{ $stream->mime_type }}">
</video>

