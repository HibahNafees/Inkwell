@extends('layouts.app', ['title' => 'Liked posts'])

@section('content')
    <section class="hero">
        <h1>Liked posts</h1>
        <p class="muted">Everything you've hearted, most recent first.</p>
    </section>

    <div class="stack">
        @forelse ($posts as $post)
            @include('partials.post-card', ['post' => $post])
        @empty
            <div class="empty card">
                <p>You haven't liked any posts yet.</p>
                <a class="btn btn-primary" href="{{ route('home') }}">Explore posts</a>
            </div>
        @endforelse
    </div>

    {{ $posts->links('partials.pagination') }}
@endsection
