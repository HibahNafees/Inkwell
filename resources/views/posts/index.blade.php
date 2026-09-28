@extends('layouts.app')

@section('content')
    <section class="hero">
        <h1>Stories from our writers</h1>
        <p class="muted">Read what people are sharing, like and comment on what moves you, or start a blog of your own.</p>
        <form method="GET" action="{{ route('home') }}" class="search">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search posts or tags…" aria-label="Search posts">
            <button class="btn" type="submit">Search</button>
        </form>
        @if ($popularTags->isNotEmpty())
            <ul class="tags tag-cloud">
                @foreach ($popularTags as $tag)
                    <li><a class="tag" href="{{ route('tags.show', $tag) }}">#{{ $tag->name }} <span class="muted">{{ $tag->posts_count }}</span></a></li>
                @endforeach
            </ul>
        @endif
    </section>

    <nav class="tabs" aria-label="Sort posts">
        <a href="{{ route('home', array_filter(['q' => $search])) }}" @class(['active' => $sort === 'latest'])>Latest</a>
        <a href="{{ route('home', array_filter(['q' => $search, 'sort' => 'popular'])) }}" @class(['active' => $sort === 'popular'])>Popular</a>
    </nav>

    @if ($search !== '')
        <p class="muted">Results for “{{ $search }}” · <a href="{{ route('home', ['sort' => $sort]) }}">clear</a></p>
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
