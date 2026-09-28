<article class="card post-card">
    @if ($showAuthor ?? true)
        <a class="byline" href="{{ route('authors.show', $post->author) }}">
            <span class="avatar">{{ \Illuminate\Support\Str::of($post->author->name)->substr(0, 1)->upper() }}</span>
            <span>{{ $post->author->name }}</span>
        </a>
    @endif
    <h2 class="post-card-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
    <p class="muted">{{ $post->summary() }}</p>
    <div class="meta small muted">
        <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->format('M j, Y') }}</time>
        <span>·</span><span>{{ $post->readingMinutes() }} min read</span>
        <span>·</span><span>{{ $post->comments_count }} {{ \Illuminate\Support\Str::plural('comment', $post->comments_count) }}</span>
    </div>
</article>
