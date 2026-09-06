<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskIsolationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_b_does_not_see_or_change_user_a_task(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $task = Task::factory()->create([
            'title' => 'Pagar conta',
            'user_id' => $userA->id,
        ]);

        $this->actingAs($userB)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Pagar conta']);

        $this->actingAs($userB)
            ->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertNotFound();

        $this->actingAs($userB)
            ->patchJson("/api/tasks/{$task->id}/archive")
            ->assertNotFound();

        $this->actingAs($userB)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertNotFound();

        $this->actingAs($userB)
            ->patchJson("/api/tasks/{$task->id}/priority", ['priority' => 'high'])
            ->assertNotFound();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Pagar conta',
            'user_id' => $userA->id,
            'done' => false,
            'archived' => false,
        ]);
    }

    public function test_user_a_can_toggle_their_own_task(): void
    {
        $userA = User::factory()->create();
        $task = Task::factory()->create([
            'title' => 'Pagar conta',
            'done' => false,
            'user_id' => $userA->id,
        ]);

        $this->actingAs($userA)
            ->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertOk()
            ->assertJsonFragment(['title' => 'Pagar conta', 'done' => true])
            ->assertJsonMissingPath('data.user_id');
    }
}
