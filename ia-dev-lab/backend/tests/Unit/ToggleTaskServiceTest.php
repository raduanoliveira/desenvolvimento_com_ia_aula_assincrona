<?php

namespace Tests\Unit;

use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\ToggleTaskService;
use Mockery;
use Tests\DomainTaskFactory;
use Tests\TestCase;

class ToggleTaskServiceTest extends TestCase
{
    public function test_it_toggles_a_task_owned_by_the_user(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $task = DomainTaskFactory::make(['id' => 3, 'title' => 'Pagar conta', 'done' => false, 'user_id' => 42]);
        $toggled = DomainTaskFactory::make(['id' => 3, 'title' => 'Pagar conta', 'done' => true, 'user_id' => 42]);

        $repository->shouldReceive('findForUser')
            ->once()
            ->with(3, 42)
            ->andReturn($task);

        $repository->shouldReceive('update')
            ->once()
            ->with($task, ['done' => true])
            ->andReturn($toggled);

        $service = new ToggleTaskService($repository);
        $result = $service->handle(3, 42);

        $this->assertTrue($result->done);
    }

    public function test_it_rejects_a_task_that_is_not_owned_by_the_user(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findForUser')->once()->with(3, 99)->andReturn(null);
        $repository->shouldNotReceive('update');

        $this->expectException(TaskNotFoundException::class);

        (new ToggleTaskService($repository))->handle(3, 99);
    }
}
