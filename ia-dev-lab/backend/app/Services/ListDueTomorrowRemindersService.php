<?php

namespace App\Services;

use App\Domain\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ListDueTomorrowRemindersService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $ownerId): Collection
    {
        $tomorrow = Carbon::today()->addDay()->toDateString();

        return $this->tasks->allForUser($ownerId)
            ->filter(function (Task $task) use ($tomorrow) {
                if ($task->archived || $task->done || $task->due_date === null) {
                    return false;
                }

                return $task->due_date === $tomorrow;
            })
            ->values();
    }
}
