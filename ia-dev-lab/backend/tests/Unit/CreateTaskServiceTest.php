<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\CreateTaskService;
use Mockery;
use Tests\TestCase;

class CreateTaskServiceTest extends TestCase
{
    public function test_it_creates_a_pending_task_with_trimmed_title(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $expected = new Task(['title' => 'Ler o enunciado', 'done' => false, 'user_id' => 42]);

        $repository->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'Ler o enunciado',
                'done' => false,
                'user_id' => 42,
            ])
            ->andReturn($expected);

        $service = new CreateTaskService($repository);
        $task = $service->handle(['title' => '  Ler o enunciado  '], 42);

        $this->assertSame('Ler o enunciado', $task->title);
        $this->assertFalse($task->done);
        $this->assertSame(42, $task->user_id);
    }
}
