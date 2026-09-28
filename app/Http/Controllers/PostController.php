<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * The public feed of every published post.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $posts = Post::published()
            ->with('author')
            ->withCount('comments')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->whereLike('title', "%{$search}%")->orWhereLike('body', "%{$search}%");
            }))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('posts.index', ['posts' => $posts, 'search' => $search]);
    }

    public function show(Post $post): View
    {
        Gate::authorize('view', $post);

        $post->load(['author', 'comments' => fn ($query) => $query->with('author')->oldest()]);

        return view('posts.show', ['post' => $post]);
    }

    public function create(): View
    {
        return view('posts.form', ['post' => new Post]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $request->user()->posts()->create([
            ...$request->safe()->only(['title', 'excerpt', 'body']),
            'published_at' => $request->input('action') === 'publish' ? now() : null,
        ]);

        return redirect()->route('posts.show', $post)
            ->with('status', $post->isPublished() ? 'Your post is live.' : 'Draft saved. Only you can see it.');
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        return view('posts.form', ['post' => $post]);
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $post->fill($request->safe()->only(['title', 'excerpt', 'body']));

        if ($request->input('action') === 'publish') {
            $post->published_at ??= now();
        } else {
            $post->published_at = null;
        }

        $post->save();

        return redirect()->route('posts.show', $post)
            ->with('status', $post->isPublished() ? 'Post updated.' : 'Saved as draft. Only you can see it.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('dashboard')->with('status', 'Post deleted.');
    }
}
