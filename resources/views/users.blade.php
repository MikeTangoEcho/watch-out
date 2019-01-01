@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">{{ __('Name') }}</th>
                    <th scope="col">{{ __('Joined At') }}</th>
                    <th scope="col">{{ __('Streams') }}</th>
                    <th scope="col">{{ __('Avg Viewers') }}</th>
                <tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->streams_count }}</td>
                    <td>{{ $user->averageViewers() }}</td>
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