<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Collection;

class ListTasksService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $ownerId, string $status = 'all'): Collection
    {
        return $this->tasks->allForUser($ownerId)
            ->filter(fn (Task $task) => ! $task->archived)
            ->filter(function (Task $task) use ($status) {
                return match ($status) {
                    'pending' => ! $task->done,
                    'done' => (bool) $task->done,
                    default => true,
                };
            })
            ->values();
    }
}
