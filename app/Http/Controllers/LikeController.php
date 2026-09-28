<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LikeController extends Controller
{
    /**
     * Posts the signed-in user has liked, most recently liked first.
     */
    public function index(Request $request): View
    {
        $posts = $request->user()->likedPosts()
            ->published()
            ->forCards()
            ->orderByPivot('created_at', 'desc')
            ->paginate(10);

        return view('likes.index', ['posts' => $posts]);
    }

    /**
     * Like the post, or unlike it if already liked.
     */
    public function toggle(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('like', $post);

        $post->likers()->toggle([$request->user()->id => ['created_at' => now()]]);

        return redirect()->to(url()->previous(route('posts.show', $post)).'#like');
    }
}
