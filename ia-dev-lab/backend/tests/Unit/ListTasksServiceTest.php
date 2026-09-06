<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\ListTasksService;
use Mockery;
use Tests\TestCase;

class ListTasksServiceTest extends TestCase
{
    public function test_it_ignores_archived_tasks_in_the_active_list(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $active = new Task(['title' => 'Continua ativa', 'done' => false, 'archived' => false]);
        $archived = new Task(['title' => 'Já arquivada', 'done' => false, 'archived' => true]);

        $repository->shouldReceive('allForUser')
            ->once()
            ->with(42)
            ->andReturn(collect([$active, $archived]));

        $service = new ListTasksService($repository);
        $result = $service->handle(42);

        $this->assertCount(1, $result);
        $this->assertSame('Continua ativa', $result->first()->title);
        $this->assertFalse($result->contains(fn (Task $task) => $task->title === 'Já arquivada'));
    }

    public function test_it_filters_only_pending_tasks_when_status_is_pending(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $pending = new Task(['title' => 'Pendente', 'done' => false, 'archived' => false]);
        $done = new Task(['title' => 'Concluída', 'done' => true, 'archived' => false]);

        $repository->shouldReceive('allForUser')
            ->once()
            ->with(7)
            ->andReturn(collect([$pending, $done]));

        $service = new ListTasksService($repository);
        $result = $service->handle(7, 'pending');

        $this->assertCount(1, $result);
        $this->assertSame('Pendente', $result->first()->title);
    }

    public function test_it_filters_only_done_tasks_when_status_is_done(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $pending = new Task(['title' => 'Pendente', 'done' => false, 'archived' => false]);
        $done = new Task(['title' => 'Concluída', 'done' => true, 'archived' => false]);

        $repository->shouldReceive('allForUser')
            ->once()
            ->with(7)
            ->andReturn(collect([$pending, $done]));

        $service = new ListTasksService($repository);
        $result = $service->handle(7, 'done');

        $this->assertCount(1, $result);
        $this->assertSame('Concluída', $result->first()->title);
    }
}
