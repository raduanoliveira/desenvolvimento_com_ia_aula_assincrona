<?php

namespace App\Providers;

use App\Identity\IdentityProviderInterface;
use App\Identity\SocialiteIdentityProvider;
use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\EloquentTaskRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(IdentityProviderInterface::class, SocialiteIdentityProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
