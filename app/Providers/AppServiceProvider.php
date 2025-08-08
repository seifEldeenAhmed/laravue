<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin;
use App\Models\Post;
use App\Policies\AdminPolicy;
use App\Policies\PostPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Admin::class, AdminPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
    }
}
