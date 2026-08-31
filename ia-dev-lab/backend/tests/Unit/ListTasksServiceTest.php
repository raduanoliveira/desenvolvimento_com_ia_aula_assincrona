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
}
