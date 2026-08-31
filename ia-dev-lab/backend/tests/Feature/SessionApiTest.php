<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_session_lookup_without_a_session(): void
    {
        $this->getJson('/api/session')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Não autenticado.'])
            ->assertJsonMissingPath('data');
    }

    public function test_it_returns_the_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->actingAs($user)
            ->getJson('/api/session')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', 'Ana Silva')
            ->assertJsonPath('data.email', 'ana@example.com');
    }

    public function test_it_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson('/api/session')
            ->assertNoContent();

        $this->assertGuest();
    }

    public function test_it_ends_the_session_even_when_already_disconnected(): void
    {
        $this->deleteJson('/api/session')->assertNoContent();
    }
}
