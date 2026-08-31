<?php

namespace Tests\Unit;

use App\Identity\IdentityUser;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\CompleteGoogleSignInService;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class CompleteGoogleSignInServiceTest extends TestCase
{
    public function test_it_creates_a_user_when_google_id_is_unknown(): void
    {
        $users = Mockery::mock(UserRepositoryInterface::class);
        $identity = new IdentityUser('google-user-1', 'Ana Silva', 'ana@example.com');
        $created = new User([
            'google_id' => 'google-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $users->shouldReceive('findByGoogleId')
            ->once()
            ->with('google-user-1')
            ->andReturn(null);

        $users->shouldReceive('create')
            ->once()
            ->with([
                'google_id' => 'google-user-1',
                'name' => 'Ana Silva',
                'email' => 'ana@example.com',
            ])
            ->andReturn($created);

        $service = new CompleteGoogleSignInService($users);

        $user = $service->handle($identity);

        $this->assertSame('ana@example.com', $user->email);
        $this->assertSame('google-user-1', $user->google_id);
    }

    public function test_it_returns_the_existing_user_for_the_same_google_id(): void
    {
        $users = Mockery::mock(UserRepositoryInterface::class);
        $identity = new IdentityUser('google-user-1', 'Ana Silva', 'ana@example.com');
        $existing = new User([
            'google_id' => 'google-user-1',
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
        ]);

        $users->shouldReceive('findByGoogleId')
            ->once()
            ->with('google-user-1')
            ->andReturn($existing);

        $users->shouldNotReceive('create');

        $service = new CompleteGoogleSignInService($users);

        $user = $service->handle($identity);

        $this->assertSame($existing, $user);
    }

    public function test_it_does_not_depend_on_the_task_repository(): void
    {
        $constructor = (new ReflectionClass(CompleteGoogleSignInService::class))->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            $this->assertNotNull($type);
            $this->assertNotSame(TaskRepositoryInterface::class, $type->getName());
        }
    }
}
