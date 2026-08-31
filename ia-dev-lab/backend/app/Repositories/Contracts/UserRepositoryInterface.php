<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByGoogleId(string $googleId): ?User;

    public function findByGithubId(string $githubId): ?User;

    public function create(array $data): User;
}
