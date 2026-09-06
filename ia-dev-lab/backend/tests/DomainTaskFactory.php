<?php

namespace Tests;

use App\Domain\Task;

final class DomainTaskFactory
{
    public static function make(array $overrides = []): Task
    {
        return new Task(
            id: $overrides['id'] ?? 1,
            title: $overrides['title'] ?? 'Tarefa',
            done: $overrides['done'] ?? false,
            archived: $overrides['archived'] ?? false,
            due_date: $overrides['due_date'] ?? null,
            priority: $overrides['priority'] ?? 'medium',
            user_id: $overrides['user_id'] ?? 1,
            created_at: $overrides['created_at'] ?? null,
        );
    }
}
