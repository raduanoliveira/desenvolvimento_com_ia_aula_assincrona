<?php

namespace App\Domain;

use RuntimeException;

class TaskNotFoundException extends RuntimeException
{
    public function __construct(public readonly int $taskId)
    {
        parent::__construct("Task {$taskId} was not found for this user.");
    }
}
