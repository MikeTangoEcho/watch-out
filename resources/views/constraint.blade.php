@extends('layouts.app')

@section('title', __('Warning! Usage limit reached'))


@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1><i class="material-icons">warning</i> {{ __('You have reached your usage limit.') }}</h1>
        @if($constraint['interval'])
            {{ __('During the last :interval', ['interval' => $constraint['interval']]) }}
        @endif
            <ul>
                <li>{{ $quota['stream_count'] }} /{{ $constraint['stream_count'] }} streams</li>
                <li>{{ $quota['stream_size'] }} /{{ $constraint['stream_size'] }} octects of stream data</li>
            </ul>
            <p>
                {{ __('You will have to wait until you can record again.') }} 
            </p>
            <p>
                {{ __('Meanwhile you can still watch others') }} <a href="{{ route('streams.index') }}">{{ __('Here') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection