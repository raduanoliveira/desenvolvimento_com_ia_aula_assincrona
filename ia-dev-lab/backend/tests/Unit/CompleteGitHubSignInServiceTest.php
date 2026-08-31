<?php

namespace Tests\Unit;

use App\Identity\IdentityUser;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\CompleteGitHubSignInService;
use Mockery;
use Tests\TestCase;

class CompleteGitHubSignInServiceTest extends TestCase
{
    public function test_it_creates_a_user_when_github_id_is_unknown(): void
    {
        $users = Mockery::mock(UserRepositoryInterface::class);
        $identity = new IdentityUser('github-user-1', 'Ana Silva', 'ana@example.com');
        $created = new User([
            'github_id' => 'github-user-1',
            'google_id' => null,
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $users->shouldReceive('findByGithubId')
            ->once()
            ->with('github-user-1')
            ->andReturn(null);

        $users->shouldNotReceive('findByGoogleId');

        $users->shouldReceive('create')
            ->once()
            ->with([
                'github_id' => 'github-user-1',
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
            ])
            ->andReturn($created);

        $service = new CompleteGitHubSignInService($users);

        $user = $service->handle($identity);

        $this->assertSame('github-user-1', $user->github_id);
        $this->assertNull($user->google_id);
    }

    public function test_it_returns_the_existing_user_for_the_same_github_id(): void
    {
        $users = Mockery::mock(UserRepositoryInterface::class);
        $identity = new IdentityUser('github-user-1', 'Ana Silva', 'ana@example.com');
        $existing = new User([
            'github_id' => 'github-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $users->shouldReceive('findByGithubId')
            ->once()
            ->with('github-user-1')
            ->andReturn($existing);

        $users->shouldNotReceive('create');
        $users->shouldNotReceive('findByGoogleId');

        $service = new CompleteGitHubSignInService($users);

        $this->assertSame($existing, $service->handle($identity));
    }

    public function test_it_does_not_reuse_a_google_user_with_the_same_email(): void
    {
        $users = Mockery::mock(UserRepositoryInterface::class);
        $identity = new IdentityUser('github-user-1', 'Ana Silva', 'ana@example.com');
        $created = new User([
            'github_id' => 'github-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $users->shouldReceive('findByGithubId')
            ->once()
            ->with('github-user-1')
            ->andReturn(null);

        $users->shouldNotReceive('findByGoogleId');

        $users->shouldReceive('create')
            ->once()
            ->with([
                'github_id' => 'github-user-1',
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
            ])
            ->andReturn($created);

        $service = new CompleteGitHubSignInService($users);

        $user = $service->handle($identity);

        $this->assertSame('github-user-1', $user->github_id);
        $this->assertNotSame('google-user-1', $user->google_id);
    }
}
