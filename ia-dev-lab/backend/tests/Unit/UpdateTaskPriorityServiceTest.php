<?php

namespace Tests\Unit;

use App\Domain\Task;
use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\UpdateTaskPriorityService;
use Mockery;
use Tests\DomainTaskFactory;
use Tests\TestCase;

class UpdateTaskPriorityServiceTest extends TestCase
{
    public function test_it_updates_priority_for_the_owner(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $task = DomainTaskFactory::make(['id' => 4, 'title' => 'Relatório', 'priority' => 'medium', 'user_id' => 42]);
        $updated = DomainTaskFactory::make(['id' => 4, 'title' => 'Relatório', 'priority' => 'high', 'user_id' => 42]);

        $repository->shouldReceive('findForUser')
            ->once()
            ->with(4, 42)
            ->andReturn($task);

        $repository->shouldReceive('update')
            ->once()
            ->with($task, ['priority' => 'high'])
            ->andReturn($updated);

        $service = new UpdateTaskPriorityService($repository);
        $result = $service->handle(4, 42, 'high');

        $this->assertSame('high', $result->priority);
    }

    public function test_it_rejects_a_task_that_is_not_owned_by_the_user(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findForUser')->once()->with(4, 99)->andReturn(null);
        $repository->shouldNotReceive('update');

        $this->expectException(TaskNotFoundException::class);

        (new UpdateTaskPriorityService($repository))->handle(4, 99, 'low');
    }
}
