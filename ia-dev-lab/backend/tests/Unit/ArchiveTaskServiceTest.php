<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\ArchiveTaskService;
use Mockery;
use Tests\TestCase;

class ArchiveTaskServiceTest extends TestCase
{
    public function test_it_archives_a_task_without_deleting_it(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $task = new Task(['title' => 'Relatório da aula', 'done' => false, 'archived' => false]);
        $archived = new Task(['title' => 'Relatório da aula', 'done' => false, 'archived' => true]);

        $repository->shouldReceive('find')
            ->once()
            ->with(7)
            ->andReturn($task);

        $repository->shouldReceive('update')
            ->once()
            ->with($task, ['archived' => true])
            ->andReturn($archived);

        $repository->shouldNotReceive('delete');

        $service = new ArchiveTaskService($repository);
        $result = $service->handle(7);

        $this->assertTrue($result->archived);
        $this->assertSame('Relatório da aula', $result->title);
    }
}
