@extends('layouts.app')

@section('title', __('Watch'))

@push('scripts')
<script src="{{ asset('js/streamer.js') }}"></script>
@endpush

@section('content')
<screen video-api="{{ route('api.streams.index') }}" video-count="15"></screen>
@endsection
