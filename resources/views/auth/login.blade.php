@extends('layouts.app', ['title' => 'Log in'])

@section('content')
    <div class="auth">
        <h1>Welcome back</h1>
        <form method="POST" action="{{ route('login') }}" class="card form">
            @csrf
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">

            <label class="check"><input type="checkbox" name="remember"> Remember me</label>
            <button class="btn btn-primary btn-block" type="submit">Log in</button>
        </form>
        <p class="muted center">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </div>
@endsection
