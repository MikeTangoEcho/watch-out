<div class="card">
    <div class="card-header">
        {{ $stream->id }} - {{ $stream->title }} {{ $stream->user->name }}
    </div>
    <div class="card-body">
        <video
            class="streamer"
            muted
            width="240"
            height="180"
            autoplay
            data-src="{{ route('streams.pull', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}"
            data-mime-type="{{ $stream->mime_type }}">
        </video>
    </div>
</div>
