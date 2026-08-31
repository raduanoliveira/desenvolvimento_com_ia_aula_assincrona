<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function all(): Collection
    {
        return Task::query()->orderByDesc('id')->get();
    }

    public function create(array $data): Task
    {
        return Task::query()->create($data);
    }

    public function find(int $id): ?Task
    {
        return Task::query()->find($id);
    }

    public function update(Task $task, array $data): Task
    {
        $task->fill($data);
        $task->save();

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
