<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user)
    {
        return $user->user_type === 1; // Only users with user_type 1 can create tasks
    }

    public function view(User $user, Task $task)
    {
        // Allow users with user_type = 1 to view all tasks
        if ($user->user_type === 1) {
            return true;
        }

        // Check if the user is assigned to the task
        return $task->users->contains('id', $user->id);
    }

    // public function changeStatus(User $user, Task $task)
    // {
    //     return $task->users->contains($user);
    // }
}
