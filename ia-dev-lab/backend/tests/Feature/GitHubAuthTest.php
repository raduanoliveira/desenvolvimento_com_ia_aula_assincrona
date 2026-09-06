<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Identity\FakeIdentityProvider;
use App\Identity\IdentityProviderInterface;
use App\Identity\IdentityUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new FakeIdentityProvider(
                redirectUrl: 'https://github.com/login/oauth/fake',
                user: new IdentityUser('github-user-1', 'Ana Silva', 'ana@example.com'),
            ));
    }

    public function test_it_redirects_to_the_github_identity_provider(): void
    {
        $this->get('/auth/github')
            ->assertRedirect('https://github.com/login/oauth/fake');
    }

    public function test_successful_callback_creates_a_github_user_opens_session_and_redirects_home(): void
    {
        $this->get('/auth/github/callback')
            ->assertRedirect('http://localhost:5173/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'github_id' => 'github-user-1',
            'google_id' => null,
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->getJson('/api/session')
            ->assertOk()
            ->assertJsonPath('data.email', 'ana@example.com')
            ->assertJsonPath('data.name', 'Ana Silva');
    }

    public function test_successful_callback_does_not_create_duplicate_github_users(): void
    {
        User::factory()->github()->create([
            'github_id' => 'github-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->get('/auth/github/callback')->assertRedirect('http://localhost:5173/');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_cancelled_consent_stays_disconnected_and_redirects_with_query(): void
    {
        $this->app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new FakeIdentityProvider(
                mode: FakeIdentityProvider::MODE_CANCELLED,
                redirectUrl: 'https://github.com/login/oauth/fake',
            ));

        $this->get('/auth/github/callback')
            ->assertRedirect('http://localhost:5173/?signin=cancelled');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_provider_error_stays_disconnected_and_redirects_with_query(): void
    {
        $this->app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new FakeIdentityProvider(
                mode: FakeIdentityProvider::MODE_ERROR,
                redirectUrl: 'https://github.com/login/oauth/fake',
            ));

        $this->get('/auth/github/callback')
            ->assertRedirect('http://localhost:5173/?signin=error');

        $this->assertGuest();
        $this->getJson('/api/session')->assertUnauthorized();
    }

    public function test_callback_error_bodies_and_redirects_never_leak_github_secrets(): void
    {
        $this->app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new FakeIdentityProvider(
                mode: FakeIdentityProvider::MODE_ERROR,
                redirectUrl: 'https://github.com/login/oauth/fake',
            ));

        $response = $this->get('/auth/github/callback');
        $payload = $response->headers->get('Location').$response->getContent();

        $this->assertStringNotContainsString((string) config('services.github.client_secret'), $payload);
        $this->assertStringNotContainsString('test-github-client-secret', $payload);
        $this->assertStringNotContainsString('GITHUB_CLIENT_SECRET', $payload);
    }
}
