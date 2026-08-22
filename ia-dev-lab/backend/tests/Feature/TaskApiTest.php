<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_tasks(): void
    {
        Task::factory()->create(['title' => 'Estudar TDD']);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Estudar TDD']);
    }

    public function test_it_creates_a_task(): void
    {
        $this->postJson('/api/tasks', ['title' => 'Comprar pão'])
            ->assertCreated()
            ->assertJsonFragment(['title' => 'Comprar pão', 'done' => false]);

        $this->assertDatabaseHas('tasks', ['title' => 'Comprar pão', 'done' => false]);
    }

    public function test_it_rejects_empty_title(): void
    {
        $this->postJson('/api/tasks', ['title' => ''])
            ->assertUnprocessable();
    }

    public function test_it_toggles_a_task(): void
    {
        $task = Task::factory()->create(['done' => false]);

        $this->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertOk()
            ->assertJsonFragment(['done' => true]);
    }

    public function test_it_deletes_a_task(): void
    {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_it_archives_a_task_and_omits_it_from_the_active_list(): void
    {
        $task = Task::factory()->create(['title' => 'Arquivar esta']);
        Task::factory()->create(['title' => 'Continua ativa']);

        $this->patchJson("/api/tasks/{$task->id}/archive")
            ->assertOk()
            ->assertJsonFragment(['title' => 'Arquivar esta', 'archived' => true]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Arquivar esta',
            'archived' => true,
        ]);

        $list = $this->getJson('/api/tasks')->assertOk();
        $ids = collect($list->json('data'))->pluck('id');

        $this->assertFalse($ids->contains($task->id));
        $list->assertJsonFragment(['title' => 'Continua ativa']);
        $list->assertJsonMissing(['title' => 'Arquivar esta']);
    }
}
