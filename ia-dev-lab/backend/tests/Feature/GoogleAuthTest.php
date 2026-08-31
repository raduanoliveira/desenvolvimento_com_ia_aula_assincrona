<?php

namespace Tests\Feature;

use App\Identity\FakeIdentityProvider;
use App\Identity\IdentityProviderInterface;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_redirects_to_the_identity_provider(): void
    {
        $this->get('/auth/google')
            ->assertRedirect('https://accounts.google.com/fake-oauth');
    }

    public function test_successful_callback_creates_a_user_opens_session_and_redirects_home(): void
    {
        $this->get('/auth/google/callback')
            ->assertRedirect('http://localhost:5173/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'google_id' => 'google-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->getJson('/api/session')
            ->assertOk()
            ->assertJsonPath('data.email', 'ana@example.com');
    }

    public function test_successful_callback_does_not_create_duplicate_users(): void
    {
        User::factory()->create([
            'google_id' => 'google-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $this->get('/auth/google/callback')->assertRedirect('http://localhost:5173/');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_cancelled_consent_stays_disconnected_and_redirects_with_query(): void
    {
        $this->app->instance(
            IdentityProviderInterface::class,
            new FakeIdentityProvider(mode: FakeIdentityProvider::MODE_CANCELLED),
        );

        $this->get('/auth/google/callback')
            ->assertRedirect('http://localhost:5173/?signin=cancelled');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_provider_error_stays_disconnected_and_redirects_with_query(): void
    {
        $this->app->instance(
            IdentityProviderInterface::class,
            new FakeIdentityProvider(mode: FakeIdentityProvider::MODE_ERROR),
        );

        $this->get('/auth/google/callback')
            ->assertRedirect('http://localhost:5173/?signin=error');

        $this->assertGuest();
        $this->getJson('/api/session')->assertUnauthorized();
    }

    public function test_callback_error_bodies_and_redirects_never_leak_google_secrets(): void
    {
        $this->app->instance(
            IdentityProviderInterface::class,
            new FakeIdentityProvider(mode: FakeIdentityProvider::MODE_ERROR),
        );

        $response = $this->get('/auth/google/callback');
        $payload = $response->headers->get('Location').$response->getContent();

        $this->assertStringNotContainsString((string) config('services.google.client_secret'), $payload);
        $this->assertStringNotContainsString('test-google-client-secret', $payload);
        $this->assertStringNotContainsString('GOOGLE_CLIENT_SECRET', $payload);
    }
}
