<?php

namespace App\Services;

use App\Domain\Task;
use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;

class ToggleTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id, int $ownerId): Task
    {
        $task = $this->tasks->findForUser($id, $ownerId);

        if ($task === null) {
            throw new TaskNotFoundException($id);
        }

        return $this->tasks->update($task, [
            'done' => ! $task->done,
        ]);
    }
}
