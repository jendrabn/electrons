<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostCommentPolicy
{
    use HandlesAuthorization;

    public function update(User $user, PostComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function delete(User $user, PostComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->id;
    }
}
