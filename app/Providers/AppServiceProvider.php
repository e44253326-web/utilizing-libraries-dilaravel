<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\ProductPolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define(
            'access-admin-dashboard',
            fn (User $user): bool => $user->role === 'admin',
        );
        Gate::define(
            'access-cashier-dashboard',
            fn (User $user): bool => $user->role === 'cashier',
        );

        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
    }
}
