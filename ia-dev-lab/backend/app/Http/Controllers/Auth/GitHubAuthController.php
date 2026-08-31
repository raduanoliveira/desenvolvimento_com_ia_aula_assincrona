<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Identity\IdentityCancelledException;
use App\Identity\IdentityFailedException;
use App\Identity\IdentityProviderInterface;
use App\Services\CompleteGitHubSignInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GitHubAuthController extends Controller
{
    public function __construct(
        private readonly IdentityProviderInterface $provider,
    ) {
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->provider->redirectUrl());
    }

    public function callback(
        Request $request,
        CompleteGitHubSignInService $completeGitHubSignIn,
    ): RedirectResponse {
        $home = rtrim((string) config('services.frontend.url'), '/');

        try {
            $identity = $this->provider->userFromCallback($request);
        } catch (IdentityCancelledException) {
            return redirect()->away($home.'/?signin=cancelled');
        } catch (IdentityFailedException) {
            return redirect()->away($home.'/?signin=error');
        }

        $user = $completeGitHubSignIn->handle($identity);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->away($home.'/');
    }
}
