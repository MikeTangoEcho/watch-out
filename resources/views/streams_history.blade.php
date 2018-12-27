@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <table class="table table-sm">
            <thead>
                <tr>
                    <td></td>
                    <td>{{ __('Title') }}</td>
                    <td>{{ __('Started At') }}</td>
                    <td>{{ __('Last Updated At') }}</td>
                    <td>{{ __('Size (Bytes)') }}</td>
                    <td>{{ __('MimeType') }}</td>
                <tr>
            </thead>
            <tbody>
                @foreach($streams as $stream)
                <tr>
                    <td>{{ $stream->id }}</td>
                    <td>{{ $stream->title }}</td>
                    <td>{{ $stream->firstChunk['created_at'] }}</td>
                    <td>{{ $stream->lastChunk['created_at'] }}</td>
                    <td>{{ $stream->total_size }}</td>
                    <td>{{ $stream->mime_type }}</td>
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