<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * The public feed of every published post.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';

        $posts = Post::published()
            ->forCards()
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->whereLike('title', "%{$search}%")
                    ->orWhereLike('body', "%{$search}%")
                    ->orWhereHas('tags', fn ($query) => $query->whereLike('name', "%{$search}%"));
            }))
            ->when($sort === 'popular', fn ($query) => $query->orderByDesc('likers_count')->orderByDesc('comments_count'))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        $popularTags = Tag::whereHas('posts', fn ($query) => $query->published())
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->orderByDesc('posts_count')
            ->limit(12)
            ->get();

        return view('posts.index', [
            'posts' => $posts,
            'search' => $search,
            'sort' => $sort,
            'popularTags' => $popularTags,
        ]);
    }

    public function show(Request $request, Post $post): View
    {
        Gate::authorize('view', $post);

        $post->load(['author', 'tags', 'comments' => fn ($query) => $query->with('author')->oldest()])
            ->loadCount('likers');

        $moreFromAuthor = $post->author->posts()
            ->published()
            ->whereKeyNot($post->id)
            ->forCards()
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('posts.show', [
            'post' => $post,
            'liked' => $post->isLikedBy($request->user()),
            'moreFromAuthor' => $moreFromAuthor,
        ]);
    }

    public function create(): View
    {
        return view('posts.form', ['post' => new Post]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $request->user()->posts()->make($request->safe()->only(['title', 'excerpt', 'body']));
        $post->published_at = $request->input('action') === 'publish' ? now() : null;

        if ($request->hasFile('cover')) {
            $post->cover_image = $request->file('cover')->store('covers', 'public');
        }

        $post->save();
        $post->syncTagsFrom($request->input('tags'));

        return redirect()->route('posts.show', $post)
            ->with('status', $post->isPublished() ? 'Your post is live.' : 'Draft saved. Only you can see it.');
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $post->load('tags');

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

        $previousCover = $post->cover_image;

        if ($request->hasFile('cover')) {
            $post->cover_image = $request->file('cover')->store('covers', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $post->cover_image = null;
        }

        $post->save();
        $post->syncTagsFrom($request->input('tags'));

        if ($previousCover && $previousCover !== $post->cover_image) {
            Storage::disk('public')->delete($previousCover);
        }

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
