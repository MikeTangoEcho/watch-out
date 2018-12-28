@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <table class="table table-sm">
            <thead>
                <tr>
                    <td></td>
                    <td>{{ __('Name') }}</td>
                    <td>{{ __('Joined At') }}</td>
                    <td>{{ __('Streams') }}</td>
                <tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->streams_count }}</td>
                <tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection