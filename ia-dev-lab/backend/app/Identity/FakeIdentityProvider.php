<?php

namespace App\Identity;

use Illuminate\Http\Request;

class FakeIdentityProvider implements IdentityProviderInterface
{
    public const MODE_SUCCESS = 'success';

    public const MODE_CANCELLED = 'cancelled';

    public const MODE_ERROR = 'error';

    public function __construct(
        private readonly string $mode = self::MODE_SUCCESS,
        private readonly string $redirectUrl = 'https://accounts.google.com/fake-oauth',
        private ?IdentityUser $user = null,
    ) {
        $this->user ??= new IdentityUser(
            providerUserId: 'google-user-1',
            name: 'Ana Silva',
            email: 'ana@example.com',
        );
    }

    public function redirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function userFromCallback(Request $request): IdentityUser
    {
        return match ($this->mode) {
            self::MODE_CANCELLED => throw new IdentityCancelledException('cancelled'),
            self::MODE_ERROR => throw new IdentityFailedException('provider error'),
            default => $this->user,
        };
    }
}
