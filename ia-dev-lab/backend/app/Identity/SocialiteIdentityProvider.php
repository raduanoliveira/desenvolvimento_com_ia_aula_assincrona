<?php

namespace App\Identity;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialiteIdentityProvider implements IdentityProviderInterface
{
    public function redirectUrl(): string
    {
        return Socialite::driver('google')->redirect()->getTargetUrl();
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
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            throw new IdentityFailedException('provider error');
        }

        return new IdentityUser(
            googleId: (string) $googleUser->getId(),
            name: (string) ($googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail()),
            email: (string) $googleUser->getEmail(),
        );
    }
}
