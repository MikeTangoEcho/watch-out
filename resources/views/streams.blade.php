@extends('layouts.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
    @foreach($streams as $stream)
        @component('components.stream', ['stream' => $stream])
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
        })
        s.openStream();
        return s;
    });
</script>
@endpush