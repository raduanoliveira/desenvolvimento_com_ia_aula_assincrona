<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArchiveTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(int $id): Task
    {
        $task = $this->tasks->find($id);

        if ($task === null) {
            throw (new ModelNotFoundException())->setModel(Task::class, [$id]);
        }

        return $this->tasks->update($task, [
            'archived' => true,
        ]);
    }
}
