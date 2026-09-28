@extends('layouts.app', ['title' => '#'.$tag->name])

@section('content')
    <section class="hero">
        <p class="muted small"><a href="{{ route('home') }}">← All posts</a></p>
        <h1>#{{ $tag->name }}</h1>
        <p class="muted">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</p>
    </section>

    <div class="stack">
        @forelse ($posts as $post)
            @include('partials.post-card', ['post' => $post])
        @empty
            <div class="empty card"><p>No published posts with this tag yet.</p></div>
        @endforelse
    </div>

    {{ $posts->links('partials.pagination') }}
@endsection
