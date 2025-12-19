<?php

namespace App\Repositories;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function () use ($userId, $date, $taskIds) {
            $tasks = Task::whereIn('id', $taskIds)
                ->where('user_id', $userId)
                ->where('task_date', $date)
                ->get();

            foreach ($tasks as $task) {
                $task->order = array_search($task->id, $taskIds);
            }

            $tasks->each->save(); // saves all tasks in the transaction
        });

        return true;
    }

    // ALTERNATIVE IMPLEMENTATION IF NEEDED FOR OPTIMUM PERFORMANCE
    /*
    public function reorderTasks(int $userId, string $date, array $taskIds): bool
    {
        if (empty($taskIds)) {
            return true;
        }

        DB::transaction(function () use ($userId, $date, $taskIds) {
            $cases = [];
            foreach ($taskIds as $index => $taskId) {
                $cases[] = "WHEN id = {$taskId} THEN {$index}";
            }
            $caseSql = implode(' ', $cases);
            $ids = implode(',', $taskIds);

            DB::update("
                UPDATE tasks
                SET `order` = CASE {$caseSql} END
                WHERE id IN ({$ids})
                AND user_id = ?
                AND task_date = ?
            ", [$userId, $date]);
        });

        return true;
    }
    */

}
