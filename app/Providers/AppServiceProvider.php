<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\Facades\Schema;
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
        if (Schema::hasTable('companies')) {
            $settings = \App\Models\Company::first() ?? new \App\Models\Company();
            $sucursales = Branch::where('active', true)->get();

            view()->share([
                'company' => $settings,
                'sucursales' => $sucursales,
            ]);
        }
    }
}
