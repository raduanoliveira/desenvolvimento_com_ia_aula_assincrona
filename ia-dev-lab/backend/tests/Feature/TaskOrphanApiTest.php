<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskOrphanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_orphans_are_omitted_from_the_authenticated_list(): void
    {
        Task::factory()->create([
            'title' => 'Tarefa anônima antiga',
            'user_id' => null,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Tarefa anônima antiga'])
            ->assertJsonPath('data', []);
    }

    public function test_google_callback_does_not_assign_orphans_to_the_first_user(): void
    {
        $orphan = Task::factory()->create([
            'title' => 'Tarefa anônima antiga',
            'user_id' => null,
        ]);

        $this->get('/auth/google/callback')->assertRedirect('http://localhost:5173/');

        $this->assertDatabaseHas('tasks', [
            'id' => $orphan->id,
            'title' => 'Tarefa anônima antiga',
            'user_id' => null,
        ]);

        $this->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonMissing(['title' => 'Tarefa anônima antiga']);

        $this->postJson('/api/tasks', ['title' => 'Minha primeira'])
            ->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'id' => $orphan->id,
            'user_id' => null,
        ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Minha primeira',
        ]);
        $this->assertNotEquals(
            Task::query()->where('title', 'Minha primeira')->value('user_id'),
            null,
        );
    }
}
