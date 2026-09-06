<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateTaskTitleService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id, int $ownerId, string $title): Task
    {
        $task = $this->tasks->findForUser($id, $ownerId);

        if ($task === null) {
            throw (new ModelNotFoundException())->setModel(Task::class, [$id]);
        }

        return $this->tasks->update($task, [
            'title' => trim($title),
        ]);
    }
}
