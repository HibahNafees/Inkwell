@extends('layouts.app', ['title' => 'My posts'])

@section('content')
    <div class="page-head">
        <div>
            <h1>My posts</h1>
            <p class="muted">Your public blog lives at <a href="{{ route('authors.show', auth()->user()) }}">{{ route('authors.show', auth()->user()) }}</a></p>
        </div>
        <a class="btn btn-primary" href="{{ route('posts.create') }}">New post</a>
    </div>

    @if ($posts->isEmpty())
        <div class="empty card">
            <p>You haven't written anything yet.</p>
            <a class="btn btn-primary" href="{{ route('posts.create') }}">Write your first post</a>
        </div>
    @else
        <div class="card table-card">
            <table>
                <thead><tr><th>Title</th><th>Status</th><th>Comments</th><th>Updated</th><th></th></tr></thead>
                <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></td>
                        <td>
                            @if ($post->isPublished())
                                <span class="badge badge-live">Published</span>
                            @else
                                <span class="badge badge-draft">Draft</span>
                            @endif
                        </td>
                        <td>{{ $post->comments_count }}</td>
                        <td class="muted small">{{ $post->updated_at->diffForHumans() }}</td>
                        <td><a href="{{ route('posts.edit', $post) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
