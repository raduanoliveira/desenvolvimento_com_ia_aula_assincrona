<?php

namespace App\Identity;

final readonly class IdentityUser
{
    public function __construct(
        public string $googleId,
        public string $name,
        public string $email,
    ) {
    }
}
