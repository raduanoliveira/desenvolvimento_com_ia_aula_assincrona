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
                'due_date' => null,
            ])
            ->andReturn($expected);

        $service = new CreateTaskService($repository);
        $task = $service->handle(['title' => '  Ler o enunciado  '], 42);

        $this->assertSame('Ler o enunciado', $task->title);
        $this->assertFalse($task->done);
        $this->assertSame(42, $task->user_id);
    }

    public function test_it_creates_a_task_with_due_date(): void
    {
        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $expected = new Task([
            'title' => 'Entregar relatório',
            'done' => false,
            'user_id' => 7,
            'due_date' => '2026-09-10',
        ]);

        $repository->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'Entregar relatório',
                'done' => false,
                'user_id' => 7,
                'due_date' => '2026-09-10',
            ])
            ->andReturn($expected);

        $service = new CreateTaskService($repository);
        $task = $service->handle([
            'title' => 'Entregar relatório',
            'due_date' => '2026-09-10',
        ], 7);

        $this->assertSame('2026-09-10', $task->due_date?->format('Y-m-d'));
    }
}
