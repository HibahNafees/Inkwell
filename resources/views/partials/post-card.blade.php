<article class="card post-card {{ $post->coverUrl() ? 'has-cover' : '' }}">
    <div class="post-card-text">
        @if ($showAuthor ?? true)
            <a class="byline" href="{{ route('authors.show', $post->author) }}">
                <span class="avatar">{{ \Illuminate\Support\Str::of($post->author->name)->substr(0, 1)->upper() }}</span>
                <span>{{ $post->author->name }}</span>
            </a>
        @endif
        <h2 class="post-card-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
        <p class="muted">{{ $post->summary() }}</p>
        @include('partials.tags', ['tags' => $post->tags])
        <div class="meta small muted">
            <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('M j, Y') }}</time>
            <span>·</span><span>{{ $post->readingMinutes() }} min read</span>
            <span>·</span><span title="Likes">♥ {{ $post->likers_count }}</span>
            <span>·</span><span title="Comments">💬 {{ $post->comments_count }}</span>
        </div>
    </div>
    @if ($post->coverUrl())
        <a href="{{ route('posts.show', $post) }}" class="post-card-cover" tabindex="-1" aria-hidden="true">
            <img src="{{ $post->coverUrl() }}" alt="" loading="lazy">
        </a>
    @endif
</article>
