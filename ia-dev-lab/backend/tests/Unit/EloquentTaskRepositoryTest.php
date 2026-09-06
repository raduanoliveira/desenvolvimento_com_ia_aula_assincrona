<?php

namespace Tests\Unit;

use App\Domain\Task as DomainTask;
use App\Models\Task as EloquentTask;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentTaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_returns_a_domain_task_instead_of_an_eloquent_model(): void
    {
        $user = User::factory()->create();
        $repository = $this->app->make(TaskRepositoryInterface::class);

        $task = $repository->create([
            'title' => 'Fechar o contrato',
            'done' => false,
            'user_id' => $user->id,
            'due_date' => '2026-09-10',
            'priority' => 'high',
        ]);

        $this->assertInstanceOf(DomainTask::class, $task);
        $this->assertNotInstanceOf(EloquentTask::class, $task);
        $this->assertSame('Fechar o contrato', $task->title);
        $this->assertSame('2026-09-10', $task->due_date);
        $this->assertSame('high', $task->priority);
        $this->assertSame($user->id, $task->user_id);
    }
}
