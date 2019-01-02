@extends('layouts.app')

@section('title', __('Home'))

@section('content')
<div class="container">
    <div class="card-deck text-center">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('Watch') }}</h5>
                <p class="card-text">{{ __('Discover what\'s happening in the world.') }}</p>
                <a class="btn shadow-sm" href="{{ route('streams.index') }}">
                    <i class="material-icons" style="color:#6574cd">face</i>
                </a>
            </div>
            <div class="card-footer">
                <small class="text-muted">~{{ $streamers }} {{ __('streaming') }}</small>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ __('Record') }}</h5>
                <p class="card-text">{{ __('Stream the world around you.') }}</p>
                <a class="btn shadow-sm" href="{{ route('streams.record') }}">
                    <i class="material-icons" style="color:#e3342f">fiber_manual_record</i>
                </a>
            </div>
            <div class="card-footer">
                <small class="text-muted">~{{ $viewers }} {{ __('watching') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection