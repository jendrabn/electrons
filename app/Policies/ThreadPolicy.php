<?php

namespace App\Policies;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create threads.
     */
    public function create(User $user): bool
    {
        return (bool) $user->id;
    }

    /**
     * Determine whether the user can update the thread.
     */
    public function update(User $user, Thread $thread): bool
    {
        return $user->id === $thread->user_id;
    }

    /**
     * Determine whether the user can delete the thread.
     */
    public function delete(User $user, Thread $thread): bool
    {
        return $user->id === $thread->user_id;
    }

    /**
     * Alias for marking a thread as done / toggling is_done. Use same rule as update.
     */
    public function toggleDone(User $user, Thread $thread): bool
    {
        return $this->update($user, $thread);
    }
}
