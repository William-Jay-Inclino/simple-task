<?php

namespace App\Http\Controllers;

use App\Contracts\TaskRepositoryInterface;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = [
            'search' => $request->query('search'),
            'date' => $request->query('date'),
        ];

        $tasks = $this->taskRepository->getAllForUser(
            $request->user()->id,
            array_filter($filters, fn ($value) => $value !== null)
        );

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskRepository->create([
            'user_id' => $request->user()->id,
            'statement' => $request->input('statement'),
            'task_date' => $request->input('task_date'),
            'is_completed' => $request->input('is_completed', false),
        ]);

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {

        $data = array_filter([
            'statement' => $request->input('statement'),
            'is_completed' => $request->input('is_completed'),
        ], fn ($value) => $value !== null);

        $updatedTask = $this->taskRepository->update($task, $data);

        return new TaskResource($updatedTask);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->taskRepository->delete($task);

        return (new TaskResource($task))->response();

    }

    /**
     * Get dates that have tasks.
     */
    public function dates(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $limit = $request->query('limit', 30);

        $dates = $this->taskRepository->getTaskDates(
            $request->user()->id,
            min($limit, 100) // Cap at 100
        );

        return response()->json([
            'data' => $dates,
        ]);
    }

    /**
     * Reorder tasks for a specific date.
     */
    public function reorder(Request $request): JsonResponse
    {

        $this->authorize('reorder', Task::class);
        
        $request->validate([
            'date' => ['required', 'date'],
            'task_ids' => ['required', 'array'],
            'task_ids.*' => ['required', 'integer', 'exists:tasks,id'],
        ]);

        $this->taskRepository->reorderTasks(
            $request->user()->id,
            $request->input('date'),
            $request->input('task_ids')
        );

        return response()->json([
            'message' => 'Tasks reordered successfully',
        ]);
    }
}
