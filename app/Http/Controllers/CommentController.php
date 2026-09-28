<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('comment', $post);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $comment = $post->comments()->make($validated);
        $comment->author()->associate($request->user());
        $comment->save();

        return redirect()->to(route('posts.show', $post).'#comment-'.$comment->id)
            ->with('status', 'Comment posted.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $post = $comment->post;
        $comment->delete();

        return redirect()->to(route('posts.show', $post).'#comments')->with('status', 'Comment deleted.');
    }
}
