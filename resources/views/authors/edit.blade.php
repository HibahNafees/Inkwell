@extends('layouts.app', ['title' => 'Profile settings'])

@section('content')
    <div class="narrow">
        <h1>Profile settings</h1>
        <form method="POST" action="{{ route('profile.update') }}" class="card form">
            @csrf @method('PUT')

            <label for="name">Display name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="100">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="username">Username <span class="muted small">(your blog address)</span></label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required maxlength="30" pattern="[a-z0-9_]+">
            @error('username') <p class="error">{{ $message }}</p> @enderror

            <label for="bio">Bio <span class="muted small">(optional)</span></label>
            <textarea id="bio" name="bio" rows="3" maxlength="280">{{ old('bio', $user->bio) }}</textarea>
            @error('bio') <p class="error">{{ $message }}</p> @enderror

            <div class="form-actions"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>

        <h2 class="section-title">Change password</h2>
        <form method="POST" action="{{ route('password.update') }}" class="card form">
            @csrf @method('PUT')

            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
            @error('current_password', 'password') <p class="error">{{ $message }}</p> @enderror

            <label for="new_password">New password</label>
            <input id="new_password" name="password" type="password" required minlength="8" autocomplete="new-password">
            @error('password', 'password') <p class="error">{{ $message }}</p> @enderror

            <label for="new_password_confirmation">Confirm new password</label>
            <input id="new_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <div class="form-actions"><button class="btn btn-primary" type="submit">Change password</button></div>
        </form>
    </div>
@endsection
