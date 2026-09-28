<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1">
</head>
<body>
    <header class="site-header">
        <div class="wrap nav">
            <a href="{{ route('home') }}" class="brand">{{ config('app.name') }}</a>
            <nav class="nav-links">
                <a href="{{ route('home') }}">Explore</a>
                @auth
                    <a href="{{ route('dashboard') }}">My posts</a>
                    <a href="{{ route('authors.show', auth()->user()) }}">My blog</a>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">Write</a>
                    <details class="menu">
                        <summary aria-label="Account menu">{{ \Illuminate\Support\Str::of(auth()->user()->name)->substr(0, 1)->upper() }}</summary>
                        <div class="menu-panel">
                            <span class="muted small">Signed in as {{ '@'.auth()->user()->username }}</span>
                            <a href="{{ route('profile.edit') }}">Profile settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="linklike">Log out</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Start writing</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="wrap main">
        @if (session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap muted small">{{ config('app.name') }} — a home for your words.</div>
    </footer>
</body>
</html>
