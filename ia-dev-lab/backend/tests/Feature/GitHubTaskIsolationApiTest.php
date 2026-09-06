<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubTaskIsolationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_github_user_does_not_see_or_change_google_user_task_with_same_email(): void
    {
        $googleUser = User::factory()->create([
            'google_id' => 'google-user-a',
            'github_id' => null,
            'email' => 'ana@example.com',
            'name' => 'Ana Google',
        ]);
        $githubUser = User::factory()->github()->create([
            'github_id' => 'github-user-b',
            'email' => 'ana@example.com',
            'name' => 'Ana GitHub',
        ]);
        $task = Task::factory()->create([
            'title' => 'Pagar conta',
            'user_id' => $googleUser->id,
        ]);

        $this->assertNotSame($googleUser->id, $githubUser->id);
        $this->assertDatabaseCount('users', 2);

        $this->actingAs($githubUser)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Pagar conta']);

        $this->actingAs($githubUser)
            ->patchJson("/api/tasks/{$task->id}/toggle")
            ->assertNotFound();

        $this->actingAs($githubUser)
            ->patchJson("/api/tasks/{$task->id}/archive")
            ->assertNotFound();

        $this->actingAs($githubUser)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Pagar conta',
            'user_id' => $googleUser->id,
            'done' => false,
            'archived' => false,
        ]);
    }

    public function test_github_user_b_does_not_see_or_change_github_user_a_task(): void
    {
        $userA = User::factory()->github()->create();
        $userB = User::factory()->github()->create();
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

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Pagar conta',
            'user_id' => $userA->id,
            'done' => false,
            'archived' => false,
        ]);
    }

    public function test_github_user_can_toggle_their_own_task(): void
    {
        $userA = User::factory()->github()->create();
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
