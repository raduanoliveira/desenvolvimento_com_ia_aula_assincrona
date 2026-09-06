<?php

namespace App\Services;

use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;

class DeleteTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id, int $ownerId): void
    {
        $task = $this->tasks->findForUser($id, $ownerId);

        if ($task === null) {
            throw new TaskNotFoundException($id);
        }

        $this->tasks->delete($task);
    }
}
