<?php

namespace App\Services;

use App\Domain\Task;
use App\Domain\TaskNotFoundException;
use App\Repositories\Contracts\TaskRepositoryInterface;

class UpdateTaskTitleService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id, int $ownerId, string $title): Task
    {
        $task = $this->tasks->findForUser($id, $ownerId);

        if ($task === null) {
            throw new TaskNotFoundException($id);
        }

        return $this->tasks->update($task, [
            'title' => trim($title),
        ]);
    }
}
