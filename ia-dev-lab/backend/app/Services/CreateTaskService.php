<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class CreateTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(array $payload): Task
    {
        return $this->tasks->create([
            'title' => trim($payload['title']),
            'done' => false,
        ]);
    }
}
