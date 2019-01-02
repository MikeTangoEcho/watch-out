@extends('layouts.app')

@section('title', __('Watch'))

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
    @foreach($streams as $stream)
        @component('components.stream_thumb', ['stream' => $stream])
        @endcomponent
    @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    // TODO Delegate to openStream on each new elements
    // TODO auto clean inactive stream and query newer
    var streamersElements = document.getElementsByClassName('streamer');
    var streamers = Array.prototype.map.call(streamersElements, function(vid){
        var s = new Streamer(vid);
        // Bare Sound Control
        // TODO report control/dismiss
        var mute = vid.parentNode.getElementsByClassName('stream-mute')[0];
        mute.addEventListener('click', function(e) {
            vid.muted = !vid.muted;
            if (vid.muted) {
                this.innerHTML = "volume_off";
                console.log("Muted", s.src);
            } else {
                this.innerHTML = "volume_up";
                console.log("Unmuted", s.src);
            }
        })
        s.openStream();
        return s;
    });
</script>
@endpush