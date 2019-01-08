@extends('layouts.app')

@section('title', __('Warning! Usage limit reached'))


@section('content')
<div class="container">
    <div class="row">
        <div class="col">
            <h1><i class="material-icons">warning</i> {{ __('You have reached your usage limit.') }}</h1>
        @if($constraint['interval'])
            {{ __('During the last') }}
            <unit
                :value="{{ $constraint['interval'] }}"
                :units="[{'unit': 60 * 24, 'format': 'Days'}, {'unit': 60, 'format': 'H'}, {'unit': 0, 'format': 'min'}]"></unit>
        @endif
            <ul>
                <li>{{ $quota['stream_count'] }} /{{ $constraint['stream_count'] }} streams</li>
                <li>
                    <unit
                        :value="{{ $quota['stream_size'] }}"
                        :units="[{'unit': 1024000, 'format': 'Mb'}, {'unit': 1024, 'format': 'Kb'}]"></unit>
                     /<unit
                        :value="{{ $constraint['stream_size'] }}"
                        :units="[{'unit': 1024000, 'format': 'Mb'}, {'unit': 1024, 'format': 'Kb'}]"></unit> octects of stream data</li>
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