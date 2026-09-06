<?php

namespace Tests\Unit;

use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\DeleteTaskService;
use Mockery;
use Tests\DomainTaskFactory;
use Tests\TestCase;

class DeleteTaskServiceTest extends TestCase
{
    public function test_it_deletes_a_task_owned_by_the_user(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $task = DomainTaskFactory::make(['id' => 3, 'title' => 'Pagar conta', 'user_id' => 42]);

        $repository->shouldReceive('findForUser')
            ->once()
            ->with(3, 42)
            ->andReturn($task);

        $repository->shouldReceive('delete')
            ->once()
            ->with($task);

        (new DeleteTaskService($repository))->handle(3, 42);
    }

    public function test_it_rejects_deleting_a_task_that_is_not_owned_by_the_user(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('findForUser')->once()->with(3, 99)->andReturn(null);
        $repository->shouldNotReceive('delete');

        $this->expectException(TaskNotFoundException::class);

        (new DeleteTaskService($repository))->handle(3, 99);
    }
}
