<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Published posts are public; drafts are visible only to their author.
     */
    public function view(?User $user, Post $post): bool
    {
        return $post->isPublished() || $user?->id === $post->user_id;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Only published posts accept comments.
     */
    public function comment(User $user, Post $post): bool
    {
        return $post->isPublished();
    }

    /**
     * Only published posts can be liked.
     */
    public function like(User $user, Post $post): bool
    {
        return $post->isPublished();
    }
}
