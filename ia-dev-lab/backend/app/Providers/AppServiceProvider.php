<?php

namespace App\Providers;

use App\Repositories\Contracts\TaskRepositoryInterface;
use App\Repositories\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
