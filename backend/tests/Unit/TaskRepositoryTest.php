<?php

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
    $this->repo = new TaskRepository();
});

test('findById returns task when exists and null when not', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $found = $this->repo->findById($task->id);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($task->id);

    $missing = $this->repo->findById(999999);
    expect($missing)->toBeNull();
});

test('create persists a task and returns model', function () {
    $data = [
        'user_id' => $this->user->id,
        'statement' => 'Test create',
        'task_date' => now()->format('Y-m-d'),
        'is_completed' => false,
    ];

    $task = $this->repo->create($data);

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'statement' => 'Test create']);
    expect($task->user_id)->toBe($this->user->id);
});

test('update modifies given task and returns fresh model', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'statement' => 'Old']);

    $updated = $this->repo->update($task, ['statement' => 'New']);

    expect($updated->statement)->toBe('New');
    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'statement' => 'New']);
});

test('delete removes the task and returns true', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $result = $this->repo->delete($task);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('toggleCompletion flips is_completed and returns fresh model', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id, 'is_completed' => false]);

    $first = $this->repo->toggleCompletion($task);
    expect($first->is_completed)->toBeTrue();

    $second = $this->repo->toggleCompletion($first);
    expect($second->is_completed)->toBeFalse();
});

test('getAllForUser returns only users tasks and applies filters', function () {
    Task::factory()->create(['user_id' => $this->user->id, 'statement' => 'Buy groceries']);
    Task::factory()->create(['user_id' => $this->user->id, 'statement' => 'Clean house', 'task_date' => now()->format('Y-m-d')]);
    Task::factory()->create(['user_id' => $this->otherUser->id, 'statement' => 'Other user task']);

    $all = $this->repo->getAllForUser($this->user->id);
    expect($all->count())->toBe(2);

    $search = $this->repo->getAllForUser($this->user->id, ['search' => 'groceries']);
    expect($search->count())->toBe(1);
    expect($search->first()->statement)->toContain('groceries');

    $date = now()->format('Y-m-d');
    $byDate = $this->repo->getAllForUser($this->user->id, ['date' => $date]);
    expect($byDate->count())->toBe(1);
});

test('getTaskDates groups by date and respects limit', function () {
    $dates = [];
    for ($i = 0; $i < 5; $i++) {
        $d = now()->subDays($i)->format('Y-m-d');
        $dates[] = $d;
        Task::factory()->count($i + 1)->create(['user_id' => $this->user->id, 'task_date' => $d]);
    }

    $result = $this->repo->getTaskDates($this->user->id, 3);

    expect(count($result))->toBe(3);
    expect($result[0])->toHaveKeys(['date', 'task_count']);
});

test('reorderTasks updates order only for specified user and date', function () {
    $date = now()->format('Y-m-d');

    $t1 = Task::factory()->create(['user_id' => $this->user->id, 'task_date' => $date, 'order' => 0]);
    $t2 = Task::factory()->create(['user_id' => $this->user->id, 'task_date' => $date, 'order' => 1]);
    $t3 = Task::factory()->create(['user_id' => $this->otherUser->id, 'task_date' => $date, 'order' => 2]);

    $this->repo->reorderTasks($this->user->id, $date, [$t2->id, $t1->id]);

    expect($t2->fresh()->order)->toBe(0);
    expect($t1->fresh()->order)->toBe(1);
    // other user's task should be unchanged
    expect($t3->fresh()->order)->toBe(2);
});
