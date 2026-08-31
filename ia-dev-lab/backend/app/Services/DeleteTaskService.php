<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id, int $ownerId): void
    {
        $task = $this->tasks->findForUser($id, $ownerId);

        if ($task === null) {
            throw (new ModelNotFoundException())->setModel(Task::class, [$id]);
        }

        $this->tasks->delete($task);
    }
}
