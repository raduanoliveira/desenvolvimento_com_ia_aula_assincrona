<?php

namespace App\Domain;

final readonly class Task
{
    public function __construct(
        public int $id,
        public string $title,
        public bool $done,
        public bool $archived,
        public ?string $due_date,
        public string $priority,
        public int $user_id,
        public mixed $created_at = null,
    ) {
    }
}
