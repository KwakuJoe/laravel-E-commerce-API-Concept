<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();


        Gate::define('delete-create-update-category', function ($user) : bool
        {
            return $user->isAdmin === true;
        });


        Gate::define('update-delete-product', function ($user, $product) : bool
        {
            return $user->id === $product->user_id;
        });

        // for all admin
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin === true) {
                return true;
            }
        });
    }
}
