<?php

namespace App\Identity;

use Illuminate\Http\Request;

interface IdentityProviderInterface
{
    public function redirectUrl(): string;

    public function userFromCallback(Request $request): IdentityUser;
}
