@extends('layouts.app')

@section('title', __('Me'))

@section('content')
<div class="container">
	<div class="card my-2">
		<div class="card-header">
			{{ __('Basic Info') }}
		</div>
		<div class="card-body">
			<form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}">
				@csrf
				@method('PUT')
				<div class="form-group">
					<input name="name" type="input" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
						value="{{ old('name', $user->name) }}" aria-describedby="nameHelp"
						required>
					@if ($errors->has('name'))
					<span class="invalid-feedback" role="alert">
						<strong>{{ $errors->first('name') }}</strong>
					</span>
					@endif
					<small id="nameHelp" class="form-text text-muted">{{ __('Name') }}</small>
				</div>
				<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
			</form>
		</div>
	</div>

	<div class="card my-2">
		<div class="card-header">
			{{ __('Change password') }}
		</div>
		<div class="card-body">
			<form method="POST" action="{{ route('users.update_password', ['user' => $user->id]) }}">
				@csrf
				@method('PUT')
				<div class="form-group">
					<input name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
						aria-describedby="oldPasswordHelp"
						required>
					@if ($errors->has('password'))
					<span class="invalid-feedback" role="alert">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
					@endif
					<small id="oldPasswordHelp" class="form-text text-muted">{{ __('Old Password') }}</small>
				</div>
				<div class="form-group">
					<input name="new_password" type="password" class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}"
						aria-describedby="newPasswordHelp"
						required>
					@if ($errors->has('new_password'))
					<span class="invalid-feedback" role="alert">
						<strong>{{ $errors->first('new_password') }}</strong>
					</span>
					@endif
					<small id="newPasswordHelp" class="form-text text-muted">{{ __('New Password') }}</small>
				</div>
				<div class="form-group">
					<input name="new_password_confirmation" type="password" class="form-control{{ $errors->has('new_password_confirmation') ? ' is-invalid' : '' }}"
						aria-describedby="newPassword2Help"
						required>
					@if ($errors->has('new_password_confirmation'))
					<span class="invalid-feedback" role="alert">
						<strong>{{ $errors->first('new_password_confirmation') }}</strong>
					</span>
					@endif
					<small id="newPassword2Help" class="form-text text-muted">{{ __('New Password Confirmation') }}</small>
				</div>
				<button type="submit" class="btn btn-primary">{{ __('Change password') }}</button>
			</form>
		</div>
	</div>
</div>
@endsection
