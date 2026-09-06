<?php

namespace App\Services;

use App\Identity\IdentityUser;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class CompleteGoogleSignInService
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function handle(IdentityUser $identity): User
    {
        $existing = $this->users->findByGoogleId($identity->providerUserId);

        if ($existing !== null) {
            return $existing;
        }

        return $this->users->create([
            'google_id' => $identity->providerUserId,
            'name' => $identity->name,
            'email' => $identity->email,
        ]);
    }
}
