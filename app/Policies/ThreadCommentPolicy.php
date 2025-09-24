<?php

namespace App\Policies;

use App\Models\ThreadComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadCommentPolicy
{
    use HandlesAuthorization;

    public function update(User $user, ThreadComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, ThreadComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function markBest(User $user, ThreadComment $comment): bool
    {
        // Only thread owner can mark best answer
        return $user->id === $comment->thread->user_id;
    }

    public function create(User $user): bool
    {
        // any authenticated user can create a comment
        return (bool) $user->id;
    }
}
