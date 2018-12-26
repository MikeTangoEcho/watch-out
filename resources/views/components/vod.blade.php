<h4>{{ $stream->id }} - {{ $stream->title }}</h4>
<video
    controls
    muted
    width="240"
    height="180"
    autoplay
    src="{{ route('stream.full', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}">
</video>

