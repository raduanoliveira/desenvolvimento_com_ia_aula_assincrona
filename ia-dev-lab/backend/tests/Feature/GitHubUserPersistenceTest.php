<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubUserPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_github_user_persists_with_github_id_and_null_google_id(): void
    {
        $user = User::factory()->create([
            'google_id' => null,
            'github_id' => 'github-user-1',
            'name' => 'Ana GitHub',
            'email' => 'ana@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'google_id' => null,
            'github_id' => 'github-user-1',
            'email' => 'ana@example.com',
        ]);
    }

    public function test_google_user_still_persists_with_google_id(): void
    {
        User::factory()->create([
            'google_id' => 'google-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'google_id' => 'google-user-1',
            'email' => 'ana@example.com',
        ]);
    }
}
