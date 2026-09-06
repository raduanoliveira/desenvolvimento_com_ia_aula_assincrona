<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class CreateTaskService
{
    public function __construct(private readonly TaskRepositoryInterface $tasks)
    {
    }

    public function handle(array $payload, int $ownerId): Task
    {
        return $this->tasks->create([
            'title' => trim($payload['title']),
            'done' => false,
            'user_id' => $ownerId,
            'due_date' => $payload['due_date'] ?? null,
        ]);
    }
}
