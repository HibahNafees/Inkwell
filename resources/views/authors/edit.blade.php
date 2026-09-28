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
    </div>
@endsection
