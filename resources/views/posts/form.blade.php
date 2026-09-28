@extends('layouts.app', ['title' => $post->exists ? 'Edit post' : 'New post'])

@section('content')
    <div class="narrow">
        <h1>{{ $post->exists ? 'Edit post' : 'Write a new post' }}</h1>

        <form method="POST" action="{{ $post->exists ? route('posts.update', $post) : route('posts.store') }}" class="card form">
            @csrf
            @if ($post->exists) @method('PUT') @endif

            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required maxlength="200" autofocus>
            @error('title') <p class="error">{{ $message }}</p> @enderror

            <label for="excerpt">Subtitle <span class="muted small">(optional — shown in the feed)</span></label>
            <input id="excerpt" name="excerpt" type="text" value="{{ old('excerpt', $post->excerpt) }}" maxlength="300">
            @error('excerpt') <p class="error">{{ $message }}</p> @enderror

            <label for="body">Story</label>
            <textarea id="body" name="body" rows="18" required placeholder="Tell your story… (leave a blank line between paragraphs)">{{ old('body', $post->body) }}</textarea>
            @error('body') <p class="error">{{ $message }}</p> @enderror

            <div class="form-actions">
                <button class="btn" type="submit" name="action" value="draft">{{ $post->isPublished() ? 'Unpublish (save as draft)' : 'Save draft' }}</button>
                <button class="btn btn-primary" type="submit" name="action" value="publish">{{ $post->isPublished() ? 'Update' : 'Publish' }}</button>
            </div>
            <p class="small muted">Drafts are private to you. Published posts appear on the home feed and your blog, where others can comment.</p>
        </form>
    </div>
@endsection
