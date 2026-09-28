@extends('layouts.app', ['title' => 'Create account'])

@section('content')
    <div class="auth">
        <h1>Start your blog</h1>
        <form method="POST" action="{{ route('register') }}" class="card form">
            @csrf
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus maxlength="100" autocomplete="name">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="username">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required minlength="3" maxlength="30" pattern="[a-z0-9_]+" placeholder="lowercase, numbers, _">
            @error('username') <p class="error">{{ $message }}</p> @enderror

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <button class="btn btn-primary btn-block" type="submit">Create account</button>
        </form>
        <p class="muted center">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
    </div>
@endsection
