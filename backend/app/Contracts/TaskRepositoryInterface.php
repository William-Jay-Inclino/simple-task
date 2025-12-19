<?php

namespace App\Contracts;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllForUser(int $userId, array $filters = []): Collection;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;

    public function getTaskDates(int $userId, int $limit = 30): array;

    public function reorderTasks(int $userId, string $date, array $taskIds): bool;
}
