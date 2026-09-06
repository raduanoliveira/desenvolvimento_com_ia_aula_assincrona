<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_it_lists_tasks(): void
    {
        Task::factory()->create([
            'title' => 'Estudar TDD',
            'user_id' => $this->user->id,
        ]);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Estudar TDD'])
            ->assertJsonMissingPath('data.0.user_id');
    }

    public function test_it_creates_a_task(): void
    {
        $this->postJson('/api/tasks', ['title' => 'Comprar pão'])
            ->assertCreated()
            ->assertJsonFragment(['title' => 'Comprar pão', 'done' => false, 'due_date' => null])
            ->assertJsonMissingPath('data.user_id');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Comprar pão',
            'done' => false,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_it_creates_a_task_with_due_date(): void
    {
        $this->postJson('/api/tasks', [
            'title' => 'Entregar relatório',
            'due_date' => '2026-09-10',
        ])
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'Entregar relatório',
                'due_date' => '2026-09-10',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Entregar relatório',
            'due_date' => '2026-09-10',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_it_rejects_invalid_due_date(): void
    {
        $this->postJson('/api/tasks', [
            'title' => 'Data inválida',
            'due_date' => '10-09-2026',
        ])->assertUnprocessable();
    }

    public function test_it_lists_due_tomorrow_reminders_for_the_owner(): void
    {
        \Carbon\Carbon::setTestNow('2026-09-06 09:00:00');

        Task::factory()->create([
            'title' => 'Lembra amanhã',
            'due_date' => '2026-09-07',
            'user_id' => $this->user->id,
        ]);
        Task::factory()->create([
            'title' => 'Outro dia',
            'due_date' => '2026-09-08',
            'user_id' => $this->user->id,
        ]);
        Task::factory()->create([
            'title' => 'De outra pessoa',
            'due_date' => '2026-09-07',
            'user_id' => User::factory()->create()->id,
        ]);

        $this->getJson('/api/reminders/due-tomorrow')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Lembra amanhã'])
            ->assertJsonMissing(['title' => 'Outro dia'])
            ->assertJsonMissing(['title' => 'De outra pessoa']);

        \Carbon\Carbon::setTestNow();
    }

    public function test_it_rejects_empty_title(): void
    {
        $this->postJson('/api/tasks', ['title' => ''])
            ->assertUnprocessable();
    }

    public function test_it_toggles_a_task(): void
    {
        $task = Task::factory()->create([
            'done' => false,
            'user_id' => $this->user->id,
        ]);

        $this->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertOk()
            ->assertJsonFragment(['done' => true]);
    }

    public function test_it_deletes_a_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_it_archives_a_task_and_omits_it_from_the_active_list(): void
    {
        $task = Task::factory()->create([
            'title' => 'Arquivar esta',
            'user_id' => $this->user->id,
        ]);
        Task::factory()->create([
            'title' => 'Continua ativa',
            'user_id' => $this->user->id,
        ]);

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
