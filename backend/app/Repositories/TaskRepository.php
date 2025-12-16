<?php

namespace App\Repositories;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllForUser(int $userId, array $filters = []): Collection
    {
        $query = Task::where('user_id', $userId);

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['date'])) {
            $query->forDate($filters['date']);
        }

        return $query->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Task
    {
        return Task::find($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function toggleCompletion(Task $task): Task
    {
        $task->is_completed = ! $task->is_completed;
        $task->save();

        return $task->fresh();
    }

    public function getTaskDates(int $userId, int $limit = 30): array
    {
        return Task::where('user_id', $userId)
            ->selectRaw('task_date as date, COUNT(*) as task_count')
            ->groupBy('task_date')
            ->orderBy('task_date', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'task_count' => $item->task_count,
                ];
            })
            ->toArray();
    }

    public function reorderTasks(int $userId, string $date, array $taskIds): bool
    {
        foreach ($taskIds as $index => $taskId) {
            Task::where('id', $taskId)
                ->where('user_id', $userId)
                ->where('task_date', $date)
                ->update(['order' => $index]);
        }

        return true;
    }
}
