@extends('layouts.app', ['title' => $post->title])

@section('content')
    <article class="post">
        @unless ($post->isPublished())
            <div class="badge badge-draft">Draft — only you can see this</div>
        @endunless

        <h1 class="post-title">{{ $post->title }}</h1>

        <div class="post-meta">
            <a class="byline" href="{{ route('authors.show', $post->author) }}">
                <span class="avatar">{{ \Illuminate\Support\Str::of($post->author->name)->substr(0, 1)->upper() }}</span>
                <span>{{ $post->author->name }}</span>
            </a>
            <span class="small muted">
                @if ($post->isPublished())
                    {{ $post->published_at->format('M j, Y') }} ·
                @endif
                {{ $post->readingMinutes() }} min read
            </span>
            @can('update', $post)
                <span class="actions">
                    <a class="btn btn-sm" href="{{ route('posts.edit', $post) }}">Edit</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post and all its comments?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                    </form>
                </span>
            @endcan
        </div>

        <div class="prose">
            @foreach (preg_split('/\R{2,}/', trim($post->body)) as $paragraph)
                <p>{!! nl2br(e($paragraph)) !!}</p>
            @endforeach
        </div>
    </article>

    @if ($post->isPublished())
        <section class="comments" id="comments">
            <h2>{{ $post->comments->count() }} {{ \Illuminate\Support\Str::plural('Comment', $post->comments->count()) }}</h2>

            @auth
                <form method="POST" action="{{ route('comments.store', $post) }}" class="card comment-form">
                    @csrf
                    <label for="body" class="sr-only">Your comment</label>
                    <textarea id="body" name="body" rows="3" placeholder="Share your thoughts…" required maxlength="5000">{{ old('body') }}</textarea>
                    @error('body') <p class="error">{{ $message }}</p> @enderror
                    <div><button class="btn btn-primary" type="submit">Post comment</button></div>
                </form>
            @else
                <p class="card muted"><a href="{{ route('login') }}">Log in</a> or <a href="{{ route('register') }}">sign up</a> to join the conversation.</p>
            @endauth

            <ol class="comment-list">
                @foreach ($post->comments as $comment)
                    <li class="comment" id="comment-{{ $comment->id }}">
                        <div class="comment-head">
                            <a class="byline" href="{{ route('authors.show', $comment->author) }}">
                                <span class="avatar avatar-sm">{{ \Illuminate\Support\Str::of($comment->author->name)->substr(0, 1)->upper() }}</span>
                                <strong>{{ $comment->author->name }}</strong>
                            </a>
                            @if ($comment->user_id === $post->user_id)
                                <span class="badge">Author</span>
                            @endif
                            <span class="small muted">{{ $comment->created_at->diffForHumans() }}</span>
                            @can('delete', $comment)
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="push-right" onsubmit="return confirm('Delete this comment?')">
                                    @csrf @method('DELETE')
                                    <button class="linklike small" type="submit">Delete</button>
                                </form>
                            @endcan
                        </div>
                        <p>{!! nl2br(e($comment->body)) !!}</p>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
@endsection
