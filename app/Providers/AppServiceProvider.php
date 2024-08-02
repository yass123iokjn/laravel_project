<?php

namespace App\Providers;

use App\Models\Formula;
use App\Policies\FormulaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Formula::class => FormulaPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
