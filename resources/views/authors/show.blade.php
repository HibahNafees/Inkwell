@extends('layouts.app', ['title' => $author->name])

@section('content')
    <section class="author-hero">
        <span class="avatar avatar-lg">{{ \Illuminate\Support\Str::of($author->name)->substr(0, 1)->upper() }}</span>
        <div>
            <h1>{{ $author->name }}</h1>
            <p class="muted small">{{ '@'.$author->username }} · writing since {{ $author->created_at->format('M Y') }}</p>
            <p class="small stats"><strong>{{ $posts->total() }}</strong> {{ \Illuminate\Support\Str::plural('post', $posts->total()) }} · <strong>{{ $totalLikes }}</strong> {{ \Illuminate\Support\Str::plural('like', $totalLikes) }}</p>
            @if ($author->bio)
                <p>{{ $author->bio }}</p>
            @endif
            @if (auth()->id() === $author->id)
                <a class="small" href="{{ route('profile.edit') }}">Edit profile</a>
            @endif
        </div>
    </section>

    <div class="stack">
        @forelse ($posts as $post)
            @include('partials.post-card', ['post' => $post, 'showAuthor' => false])
        @empty
            <div class="empty card"><p>No published posts yet.</p></div>
        @endforelse
    </div>

    {{ $posts->links('partials.pagination') }}
@endsection
