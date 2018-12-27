@extends('layouts.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush

@section('content')
<div class="container">
    <div class="row">
    @component('components.stream', ['stream' => $stream])
    @endcomponent
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    // TODO Delegate to openStream on each new elements
    var streamersElements = document.getElementsByClassName('streamer');
    var streamers = Array.prototype.map.call(streamersElements, function(e){
        var s = new Streamer(e);
        s.openStream();
        return s;
    });
</script>
@endpush