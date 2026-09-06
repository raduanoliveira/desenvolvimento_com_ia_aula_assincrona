<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Services\ListDueTomorrowRemindersService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class ListDueTomorrowRemindersServiceTest extends TestCase
{
    public function test_it_returns_only_active_incomplete_tasks_due_tomorrow(): void
    {
        Carbon::setTestNow('2026-09-06 10:00:00');

        $tomorrow = new Task([
            'id' => 1,
            'title' => 'Prova amanhã',
            'done' => false,
            'archived' => false,
            'due_date' => '2026-09-07',
            'user_id' => 3,
        ]);
        $today = new Task([
            'id' => 2,
            'title' => 'Hoje',
            'done' => false,
            'archived' => false,
            'due_date' => '2026-09-06',
            'user_id' => 3,
        ]);
        $done = new Task([
            'id' => 3,
            'title' => 'Já feita',
            'done' => true,
            'archived' => false,
            'due_date' => '2026-09-07',
            'user_id' => 3,
        ]);
        $archived = new Task([
            'id' => 4,
            'title' => 'Arquivada',
            'done' => false,
            'archived' => true,
            'due_date' => '2026-09-07',
            'user_id' => 3,
        ]);

        $repository = Mockery::mock(TaskRepositoryInterface::class);
        $repository->shouldReceive('allForUser')
            ->once()
            ->with(3)
            ->andReturn(new Collection([$tomorrow, $today, $done, $archived]));

        $service = new ListDueTomorrowRemindersService($repository);
        $reminders = $service->handle(3);

        $this->assertCount(1, $reminders);
        $this->assertSame('Prova amanhã', $reminders->first()->title);

        Carbon::setTestNow();
    }
}
