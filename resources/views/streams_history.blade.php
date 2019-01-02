@extends('layouts.app')

@section('title', __('History'))

@section('content')
<div class="container">
    <div class="row">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">{{ __('Title') }}</th>
                    <th scope="col">{{ __('Started At') }}</th>
                    <th scope="col">{{ __('Last Updated At') }}</th>
                    <th scope="col">{{ __('Size (Bytes)') }}</th>
                    <th scope="col">{{ __('MimeType') }}</th>
                    <th scope="col">{{ __('Max Viewers') }}</th>
                    <th scope="col">{{ __('Watch') }}</th>
                <tr>
            </thead>
            <tbody>
                @foreach($streams as $stream)
                <tr>
                    <th scope="row">{{ $stream->id }}</th>
                    <td>{{ $stream->title }}</td>
                    <td>{{ $stream->firstChunk['created_at'] }}</td>
                    <td>{{ $stream->lastChunk['created_at'] }}</td>
                    <td>{{ $stream->total_size }}</td>
                    <td>{{ $stream->mime_type }}</td>
                    <td>{{ $stream->maxViewers() }}</td>
                    <td>
                        <a href="{{ route('streams.full', ['stream' => $stream->id]) }}">
                            <i class="material-icons">play_circle_outline</i>
                        </a>
                    </td>
                <tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row justify-content-center">
        {{ $streams->links() }}
    </div>
</div>
@endsection