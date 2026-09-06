<?php

namespace App\Identity;

final readonly class IdentityUser
{
    public function __construct(
        public string $providerUserId,
        public string $name,
        public string $email,
    ) {
    }
}
