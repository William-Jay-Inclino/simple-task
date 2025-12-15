<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'web');
    }

    // Index Tests
    public function test_can_list_all_tasks_for_authenticated_user(): void
    {
        Task::factory()->count(3)->create(['user_id' => $this->user->id]);
        Task::factory()->count(2)->create(); // Other user's tasks

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_tasks_by_completion_status(): void
    {
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
        ]);
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
        ]);

        $response = $this->getJson('/api/tasks?is_completed=1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_search_tasks_by_statement(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Buy groceries',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Clean the house',
        ]);

        $response = $this->getJson('/api/tasks?search=groceries');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.statement', 'Buy groceries');
    }

    public function test_index_requires_authentication(): void
    {
        auth()->logout();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    // Store Tests
    public function test_can_create_a_new_task(): void
    {
        $data = [
            'statement' => 'New task to complete',
            'is_completed' => false,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.statement', 'New task to complete')
            ->assertJsonPath('data.is_completed', false)
            ->assertJsonPath('data.user_id', $this->user->id);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'statement' => 'New task to complete',
            'is_completed' => false,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['statement']);
    }

    public function test_store_validates_statement_max_length(): void
    {
        $response = $this->postJson('/api/tasks', [
            'statement' => str_repeat('a', 5001),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['statement']);
    }

    public function test_store_validates_is_completed_must_be_boolean(): void
    {
        $response = $this->postJson('/api/tasks', [
            'statement' => 'Test task',
            'is_completed' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['is_completed']);
    }

    public function test_store_requires_authentication(): void
    {
        auth()->logout();

        $response = $this->postJson('/api/tasks', [
            'statement' => 'Test task',
        ]);

        $response->assertStatus(401);
    }

    // Show Tests
    public function test_can_retrieve_a_specific_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Specific task',
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.statement', 'Specific task');
    }

    public function test_cannot_view_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_show_returns_404_for_non_existent_task(): void
    {
        $response = $this->getJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    public function test_show_requires_authentication(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        auth()->logout();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(401);
    }

    // Update Tests
    public function test_can_update_task_statement(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Original statement',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'statement' => 'Updated statement',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.statement', 'Updated statement');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'statement' => 'Updated statement',
        ]);
    }

    public function test_can_update_only_is_completed(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Original statement',
            'is_completed' => false,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'is_completed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.is_completed', true)
            ->assertJsonPath('data.statement', 'Original statement');
    }

    public function test_can_update_both_statement_and_is_completed(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'statement' => 'Original',
            'is_completed' => false,
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'statement' => 'Updated',
            'is_completed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.statement', 'Updated')
            ->assertJsonPath('data.is_completed', true);
    }

    public function test_update_validates_statement_max_length(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'statement' => str_repeat('a', 5001),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['statement']);
    }

    public function test_cannot_update_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'statement' => 'Hacked',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_requires_authentication(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        auth()->logout();

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'statement' => 'Updated',
        ]);

        $response->assertStatus(401);
    }

    // Delete Tests
    public function test_can_delete_a_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_cannot_delete_another_users_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_delete_returns_404_when_deleting_non_existent_task(): void
    {
        $response = $this->deleteJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    public function test_delete_requires_authentication(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);
        auth()->logout();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(401);
    }

    // Dates Tests
    public function test_can_fetch_task_dates(): void
    {
        // Create tasks on different dates
        Task::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(1),
        ]);
        Task::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(2),
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(3),
        ]);

        // Create task for another user (should not appear)
        Task::factory()->create([
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/api/tasks/dates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['date', 'task_count'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_dates_endpoint_limits_results(): void
    {
        // Create tasks on 50 different dates
        for ($i = 0; $i < 50; $i++) {
            Task::factory()->create([
                'user_id' => $this->user->id,
                'created_at' => now()->subDays($i),
            ]);
        }

        $response = $this->getJson('/api/tasks/dates?limit=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }

    public function test_dates_requires_authentication(): void
    {
        auth()->logout();

        $response = $this->getJson('/api/tasks/dates');

        $response->assertStatus(401);
    }
}
