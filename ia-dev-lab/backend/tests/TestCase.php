<?php

namespace Tests;

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

        $app->bind(IdentityProviderInterface::class, FakeIdentityProvider::class);

        return $app;
    }
}
