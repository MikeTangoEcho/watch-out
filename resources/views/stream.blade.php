@extends('layout.app')

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush


@section('content')
    {{ include('components.stream') }}
@endsection