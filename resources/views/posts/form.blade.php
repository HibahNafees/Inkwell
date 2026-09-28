@extends('layouts.app', ['title' => $post->exists ? 'Edit post' : 'New post'])

@section('content')
    <div class="narrow">
        <h1>{{ $post->exists ? 'Edit post' : 'Write a new post' }}</h1>

        <form method="POST" action="{{ $post->exists ? route('posts.update', $post) : route('posts.store') }}" enctype="multipart/form-data" class="card form">
            @csrf
            @if ($post->exists) @method('PUT') @endif

            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $post->title) }}" required maxlength="200" autofocus>
            @error('title') <p class="error">{{ $message }}</p> @enderror

            <label for="excerpt">Subtitle <span class="muted small">(optional — shown in the feed)</span></label>
            <input id="excerpt" name="excerpt" type="text" value="{{ old('excerpt', $post->excerpt) }}" maxlength="300">
            @error('excerpt') <p class="error">{{ $message }}</p> @enderror

            <label for="cover">Cover image <span class="muted small">(optional — JPG, PNG, WebP or GIF, up to 2 MB)</span></label>
            @if ($post->coverUrl())
                <img class="cover-preview" src="{{ $post->coverUrl() }}" alt="Current cover">
                <label class="check"><input type="checkbox" name="remove_cover" value="1"> Remove current cover</label>
            @endif
            <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
            @error('cover') <p class="error">{{ $message }}</p> @enderror

            <label for="body">Story</label>
            <textarea id="body" name="body" rows="18" required placeholder="Tell your story…">{{ old('body', $post->body) }}</textarea>
            <p class="small muted hint">Formatting: <code>**bold**</code> · <code>*italic*</code> · <code>## Heading</code> · <code>[link](https://…)</code> · <code>&gt; quote</code> · <code>- list</code></p>
            @error('body') <p class="error">{{ $message }}</p> @enderror

            <label for="tags">Tags <span class="muted small">(optional — up to 5, separated by commas)</span></label>
            <input id="tags" name="tags" type="text" value="{{ old('tags', $post->exists ? $post->tags->pluck('name')->implode(', ') : '') }}" maxlength="200" placeholder="travel, food, life">
            @error('tags') <p class="error">{{ $message }}</p> @enderror

            <div class="form-actions">
                <button class="btn" type="submit" name="action" value="draft">{{ $post->isPublished() ? 'Unpublish (save as draft)' : 'Save draft' }}</button>
                <button class="btn btn-primary" type="submit" name="action" value="publish">{{ $post->isPublished() ? 'Update' : 'Publish' }}</button>
            </div>
            <p class="small muted">Drafts are private to you. Published posts appear on the home feed and your blog, where others can like and comment.</p>
        </form>
    </div>
@endsection
