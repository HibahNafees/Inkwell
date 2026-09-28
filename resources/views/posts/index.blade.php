@extends('layouts.app')

@section('content')
    <section class="hero">
        <h1>Stories from our writers</h1>
        <p class="muted">Read what people are sharing, join the conversation, or start a blog of your own.</p>
        <form method="GET" action="{{ route('home') }}" class="search">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search posts…" aria-label="Search posts">
            <button class="btn" type="submit">Search</button>
        </form>
    </section>

    @if ($search !== '')
        <p class="muted">Results for “{{ $search }}” · <a href="{{ route('home') }}">clear</a></p>
    @endif

    <div class="stack">
        @forelse ($posts as $post)
            @include('partials.post-card', ['post' => $post])
        @empty
            <div class="empty card">
                <p>No posts {{ $search !== '' ? 'match your search' : 'yet' }}.</p>
                @auth
                    <a class="btn btn-primary" href="{{ route('posts.create') }}">Write the first one</a>
                @else
                    <a class="btn btn-primary" href="{{ route('register') }}">Create an account to write</a>
                @endauth
            </div>
        @endforelse
    </div>

    {{ $posts->links('partials.pagination') }}
@endsection
