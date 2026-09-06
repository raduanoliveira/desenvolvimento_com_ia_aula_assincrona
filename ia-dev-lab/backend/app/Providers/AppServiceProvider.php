<?php

namespace App\Providers;

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
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

        $this->app->when(GoogleAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new SocialiteIdentityProvider('google'));

        $this->app->when(GitHubAuthController::class)
            ->needs(IdentityProviderInterface::class)
            ->give(fn () => new SocialiteIdentityProvider('github'));
    }

    public function boot(): void
    {
        //
    }
}
