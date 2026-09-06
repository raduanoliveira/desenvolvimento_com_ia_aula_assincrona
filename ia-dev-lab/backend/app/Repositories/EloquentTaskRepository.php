<?php

namespace App\Repositories;

use App\Domain\Task as DomainTask;
use App\Models\Task as EloquentTask;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function all(): Collection
    {
        return EloquentTask::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentTask $task) => $this->toDomain($task))
            ->values();
    }

    public function allForUser(int $userId): Collection
    {
        return EloquentTask::query()
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(fn (EloquentTask $task) => $this->toDomain($task))
            ->values();
    }

    public function create(array $data): DomainTask
    {
        return $this->toDomain(EloquentTask::query()->create($data));
    }

    public function find(int $id): ?DomainTask
    {
        $task = EloquentTask::query()->find($id);

        return $task === null ? null : $this->toDomain($task);
    }

    public function findForUser(int $id, int $userId): ?DomainTask
    {
        $task = EloquentTask::query()
            ->whereKey($id)
            ->where('user_id', $userId)
            ->first();

        return $task === null ? null : $this->toDomain($task);
    }

    public function update(DomainTask $task, array $data): DomainTask
    {
        $model = EloquentTask::query()->findOrFail($task->id);
        $model->fill($data);
        $model->save();

        return $this->toDomain($model);
    }

    public function delete(DomainTask $task): void
    {
        EloquentTask::query()->whereKey($task->id)->delete();
    }

    private function toDomain(EloquentTask $task): DomainTask
    {
        $dueDate = $task->due_date;

        return new DomainTask(
            id: (int) $task->id,
            title: (string) $task->title,
            done: (bool) $task->done,
            archived: (bool) $task->archived,
            due_date: $dueDate === null
                ? null
                : ($dueDate instanceof CarbonInterface ? $dueDate->toDateString() : (string) $dueDate),
            priority: (string) ($task->priority ?? 'medium'),
            user_id: (int) $task->user_id,
            created_at: $task->created_at,
        );
    }
}
