<?php

namespace App\Services;

use App\Models\Task;
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
                if ($task->archived || $task->done) {
                    return false;
                }

                $dueDate = $task->due_date;

                if ($dueDate === null) {
                    return false;
                }

                $normalized = $dueDate instanceof Carbon
                    ? $dueDate->toDateString()
                    : Carbon::parse((string) $dueDate)->toDateString();

                return $normalized === $tomorrow;
            })
            ->values();
    }
}
