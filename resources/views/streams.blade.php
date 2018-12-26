@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush


@section('content')
    <div class="container">
    @foreach($streams as $stream)
        @component('components.stream', ['stream' => $stream])
        @endcomponent
    @endforeach
    </div>
@endsection