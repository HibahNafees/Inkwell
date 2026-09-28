<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * The signed-in author's own posts, drafts included.
     */
    public function __invoke(Request $request): View
    {
        $posts = $request->user()->posts()
            ->withCount(['comments', 'likers'])
            ->orderByRaw('published_at is null desc')
            ->latest('updated_at')
            ->get();

        return view('dashboard', ['posts' => $posts]);
    }
}
