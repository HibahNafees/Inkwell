<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthorController extends Controller
{
    /**
     * An author's personal blog: their published posts.
     */
    public function show(User $user): View
    {
        $posts = $user->posts()
            ->published()
            ->forCards()
            ->latest('published_at')
            ->paginate(10);

        $totalLikes = $user->posts()->published()->withCount('likers')->get()->sum('likers_count');

        return view('authors.show', ['author' => $user, 'posts' => $posts, 'totalLikes' => $totalLikes]);
    }

    public function edit(Request $request): View
    {
        return view('authors.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9_]+$/', Rule::unique('users')->ignore($user)],
            'bio' => ['nullable', 'string', 'max:280'],
        ]);

        $user->update($validated);

        return redirect()->route('authors.show', $user)->with('status', 'Profile updated.');
    }
}
