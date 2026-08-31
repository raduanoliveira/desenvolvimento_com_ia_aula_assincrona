<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function all(): Collection;

    public function allForUser(int $userId): Collection;

    public function create(array $data): Task;

    public function find(int $id): ?Task;

    public function findForUser(int $id, int $userId): ?Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): void;
}
