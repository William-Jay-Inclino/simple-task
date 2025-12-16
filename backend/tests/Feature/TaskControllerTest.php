<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user, 'web');
});

// Index Tests
test('can list all tasks for authenticated user', function () {
    Task::factory()->count(3)->create(['user_id' => $this->user->id]);
    Task::factory()->count(2)->create(); // Other user's tasks

    $this->getJson('/api/tasks')
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can search tasks by statement', function () {
    Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Buy groceries',
    ]);
    Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Clean the house',
    ]);

    $this->getJson('/api/tasks?search=groceries')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.statement', 'Buy groceries');
});

test('index requires authentication', function () {
    auth()->logout();

    $this->getJson('/api/tasks')
        ->assertStatus(401);
});

// Store Tests
test('can create a new task', function () {
    $data = [
        'statement' => 'New task to complete',
        'task_date' => now()->format('Y-m-d'),
        'is_completed' => false,
    ];

    $this->postJson('/api/tasks', $data)
        ->assertStatus(201)
        ->assertJsonPath('data.statement', 'New task to complete')
        ->assertJsonPath('data.is_completed', false)
        ->assertJsonPath('data.user_id', $this->user->id);

    $this->assertDatabaseHas('tasks', [
        'user_id' => $this->user->id,
        'statement' => 'New task to complete',
        'is_completed' => false,
    ]);
});

test('store validates required fields', function () {
    $this->postJson('/api/tasks', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['statement', 'task_date']);
});

test('store validates statement max length', function () {
    $this->postJson('/api/tasks', [
        'statement' => str_repeat('a', 5001),
        'task_date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['statement']);
});

test('store validates is_completed must be boolean', function () {
    $this->postJson('/api/tasks', [
        'statement' => 'Test task',
        'task_date' => now()->format('Y-m-d'),
        'is_completed' => 'invalid',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['is_completed']);
});

test('store requires authentication', function () {
    auth()->logout();

    $this->postJson('/api/tasks', [
        'statement' => 'Test task',
        'task_date' => now()->format('Y-m-d'),
    ])
        ->assertStatus(401);
});

// Show Tests
test('can retrieve a specific task', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Specific task',
    ]);

    $this->getJson("/api/tasks/{$task->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $task->id)
        ->assertJsonPath('data.statement', 'Specific task');
});

test('cannot view another users task', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $this->getJson("/api/tasks/{$task->id}")
        ->assertStatus(403);
});

test('show returns 404 for non existent task', function () {
    $this->getJson('/api/tasks/99999')
        ->assertStatus(404);
});

test('show requires authentication', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);
    auth()->logout();

    $this->getJson("/api/tasks/{$task->id}")
        ->assertStatus(401);
});

// Update Tests
test('can update task statement', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Original statement',
    ]);

    $this->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Updated statement',
    ])
        ->assertStatus(200)
        ->assertJsonPath('data.statement', 'Updated statement');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'statement' => 'Updated statement',
    ]);
});

test('can update only is_completed', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Original statement',
        'is_completed' => false,
    ]);

    $this->putJson("/api/tasks/{$task->id}", [
        'is_completed' => true,
    ])
        ->assertStatus(200)
        ->assertJsonPath('data.is_completed', true)
        ->assertJsonPath('data.statement', 'Original statement');
});

test('can update both statement and is_completed', function () {
    $task = Task::factory()->create([
        'user_id' => $this->user->id,
        'statement' => 'Original',
        'is_completed' => false,
    ]);

    $this->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Updated',
        'is_completed' => true,
    ])
        ->assertStatus(200)
        ->assertJsonPath('data.statement', 'Updated')
        ->assertJsonPath('data.is_completed', true);
});

test('update validates statement max length', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $this->putJson("/api/tasks/{$task->id}", [
        'statement' => str_repeat('a', 5001),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['statement']);
});

test('cannot update another users task', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $this->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Hacked',
    ])
        ->assertStatus(403);
});

test('update requires authentication', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);
    auth()->logout();

    $this->putJson("/api/tasks/{$task->id}", [
        'statement' => 'Updated',
    ])
        ->assertStatus(401);
});

// Delete Tests
test('can delete a task', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);

    $this->deleteJson("/api/tasks/{$task->id}")
        ->assertStatus(204);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('cannot delete another users task', function () {
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $this->deleteJson("/api/tasks/{$task->id}")
        ->assertStatus(403);

    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});

test('delete returns 404 when deleting non existent task', function () {
    $this->deleteJson('/api/tasks/99999')
        ->assertStatus(404);
});

test('delete requires authentication', function () {
    $task = Task::factory()->create(['user_id' => $this->user->id]);
    auth()->logout();

    $this->deleteJson("/api/tasks/{$task->id}")
        ->assertStatus(401);
});

// Dates Tests
test('can fetch task dates', function () {
    $date1 = now()->subDays(1)->format('Y-m-d');
    $date2 = now()->subDays(2)->format('Y-m-d');
    $date3 = now()->subDays(3)->format('Y-m-d');

    Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => $date1,
    ]);
    Task::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'task_date' => $date2,
    ]);
    Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => $date3,
    ]);

    Task::factory()->create([
        'task_date' => $date1,
    ]); // Another user's task

    $this->getJson('/api/tasks/dates')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['date', 'task_count'],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

test('dates endpoint limits results', function () {
    for ($i = 0; $i < 50; $i++) {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'task_date' => now()->subDays($i)->format('Y-m-d'),
        ]);
    }

    $this->getJson('/api/tasks/dates?limit=10')
        ->assertStatus(200)
        ->assertJsonCount(10, 'data');
});

test('dates requires authentication', function () {
    auth()->logout();

    $this->getJson('/api/tasks/dates')
        ->assertStatus(401);
});

// Reorder Tests
test('can reorder tasks', function () {
    $date = now()->format('Y-m-d');
    $task1 = Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => $date,
        'order' => 0,
    ]);
    $task2 = Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => $date,
        'order' => 1,
    ]);
    $task3 = Task::factory()->create([
        'user_id' => $this->user->id,
        'task_date' => $date,
        'order' => 2,
    ]);

    $this->postJson('/api/tasks/reorder', [
        'date' => $date,
        'task_ids' => [$task3->id, $task1->id, $task2->id],
    ])
        ->assertStatus(200)
        ->assertJson(['message' => 'Tasks reordered successfully']);

    expect($task3->fresh()->order)->toBe(0);
    expect($task1->fresh()->order)->toBe(1);
    expect($task2->fresh()->order)->toBe(2);
});

test('reorder requires authentication', function () {
    auth()->logout();

    $this->postJson('/api/tasks/reorder', [
        'date' => now()->format('Y-m-d'),
        'task_ids' => [1, 2, 3],
    ])
        ->assertStatus(401);
});

test('reorder validates required fields', function () {
    $this->postJson('/api/tasks/reorder', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['date', 'task_ids']);
});
