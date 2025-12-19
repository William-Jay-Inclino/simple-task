<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    
    public function viewAny(User $user): bool
    {
        return true;
    }
  
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }
   
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function reorder(User $user, array $taskIds, string $date): bool
    {
        $tasks = Task::whereIn('id', $taskIds)->get();

        // Ensure all tasks belong to the user
        if ($tasks->contains(fn ($task) => $task->user_id !== $user->id)) {
            return false;
        }

        // Ensure all tasks have the same task_date as the request date
        if ($tasks->contains(fn ($task) => $task->task_date->toDateString() !== $date)) {
            return false;
        }

        return true;
    }

}
