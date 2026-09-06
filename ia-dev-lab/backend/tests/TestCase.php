<?php

namespace Tests;

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Identity\FakeIdentityProvider;
use App\Identity\IdentityProviderInterface;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        $app->when(GoogleAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(FakeIdentityProvider::class);

        $app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(FakeIdentityProvider::class);

        return $app;
    }
}
