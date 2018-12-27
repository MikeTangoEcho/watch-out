<div class="stream"
    style="position: relative;width: 160px; height: 120px;">
    <span class="stream-title"
        style="position: absolute;top: 5px;left: 5px;color: white;font-size: 9px;">
        {{ $stream->user->name }} -- {{ $stream->title }}
    </span>
    <span class="stream-mute"
        style="position: absolute;top: 105px;left: 135px;color: white;font-size: 9px;z-index:2">
        Mute
    </span>
    <video
        class="streamer"
        muted
        width="160"
        height="120"
        autoplay
        data-src="{{ route('streams.pull', ['stream' => $stream->id, 'ts' => now()->timestamp]) }}"
        data-mime-type="{{ $stream->mime_type }}">
    </video>
</div>
