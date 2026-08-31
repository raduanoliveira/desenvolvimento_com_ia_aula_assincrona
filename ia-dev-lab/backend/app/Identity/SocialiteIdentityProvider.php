<?php

namespace App\Identity;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialiteIdentityProvider implements IdentityProviderInterface
{
    public function __construct(
        private readonly string $driver = 'google',
    ) {
    }

    public function redirectUrl(): string
    {
        return Socialite::driver($this->driver)->redirect()->getTargetUrl();
    }

    public function userFromCallback(Request $request): IdentityUser
    {
        $error = $request->query('error');

        if ($error === 'access_denied' || $error === 'consent_required') {
            throw new IdentityCancelledException('cancelled');
        }

        if (is_string($error) && $error !== '') {
            throw new IdentityFailedException('provider error');
        }

        try {
            $socialUser = Socialite::driver($this->driver)->user();
        } catch (Throwable) {
            throw new IdentityFailedException('provider error');
        }

        return new IdentityUser(
            providerUserId: (string) $socialUser->getId(),
            name: (string) ($socialUser->getName() ?: $socialUser->getNickname() ?: $socialUser->getEmail()),
            email: (string) ($socialUser->getEmail() ?? ''),
        );
    }
}
