<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Identity\IdentityCancelledException;
use App\Identity\IdentityFailedException;
use App\Identity\IdentityProviderInterface;
use App\Services\CompleteGoogleSignInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirect(IdentityProviderInterface $provider): RedirectResponse
    {
        return redirect()->away($provider->redirectUrl());
    }

    public function callback(
        Request $request,
        IdentityProviderInterface $provider,
        CompleteGoogleSignInService $completeGoogleSignIn,
    ): RedirectResponse {
        $home = rtrim((string) config('services.frontend.url'), '/');

        try {
            $identity = $provider->userFromCallback($request);
        } catch (IdentityCancelledException) {
            return redirect()->away($home.'/?signin=cancelled');
        } catch (IdentityFailedException) {
            return redirect()->away($home.'/?signin=error');
        }

        $user = $completeGoogleSignIn->handle($identity);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->away($home.'/');
    }
}
