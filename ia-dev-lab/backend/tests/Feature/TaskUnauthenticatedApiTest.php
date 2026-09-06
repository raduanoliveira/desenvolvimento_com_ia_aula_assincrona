<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskUnauthenticatedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_listing_tasks_without_a_session(): void
    {
        Task::factory()->create(['title' => 'Não deve vazar']);

        $this->getJson('/api/tasks')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Não autenticado.'])
            ->assertJsonMissingPath('data');
    }

    public function test_it_rejects_creating_a_task_without_a_session(): void
    {
        $this->postJson('/api/tasks', ['title' => 'Comprar pão'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Não autenticado.']);

        $this->assertDatabaseMissing('tasks', ['title' => 'Comprar pão']);
    }

    public function test_it_rejects_toggling_a_task_without_a_session(): void
    {
        $task = Task::factory()->create(['done' => false]);

        $this->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertUnauthorized();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'done' => false]);
    }

    public function test_it_rejects_archiving_a_task_without_a_session(): void
    {
        $task = Task::factory()->create(['archived' => false]);

        $this->patchJson("/api/tasks/{$task->id}/archive")
            ->assertUnauthorized();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'archived' => false]);
    }

    public function test_it_rejects_deleting_a_task_without_a_session(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertUnauthorized();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
