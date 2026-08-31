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

    public function handle(): Collection
    {
        return $this->tasks->all()
            ->filter(fn (Task $task) => ! $task->archived)
            ->values();
    }
}
