<?php

namespace Tests\Unit;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\ArchiveTaskService;
use Mockery;
use Tests\DomainTaskFactory;
use Tests\TestCase;

class ArchiveTaskServiceTest extends TestCase
{
    public function test_it_archives_a_task_without_deleting_it(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $task = DomainTaskFactory::make([
            'id' => 7,
            'title' => 'Relatório da aula',
            'done' => false,
            'archived' => false,
            'user_id' => 42,
        ]);
        $archived = DomainTaskFactory::make([
            'id' => 7,
            'title' => 'Relatório da aula',
            'done' => false,
            'archived' => true,
            'user_id' => 42,
        ]);

        $repository->shouldReceive('findForUser')
            ->once()
            ->with(7, 42)
            ->andReturn($task);

        $repository->shouldReceive('update')
            ->once()
            ->with($task, ['archived' => true])
            ->andReturn($archived);

        $repository->shouldNotReceive('delete');

        $service = new ArchiveTaskService($repository);
        $result = $service->handle(7, 42);

        $this->assertTrue($result->archived);
        $this->assertSame('Relatório da aula', $result->title);
    }
}
