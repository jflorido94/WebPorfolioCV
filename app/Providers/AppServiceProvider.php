<?php

namespace App\Providers;

use App\Models\User;
use App\Services\GeoLookupService;
use App\Services\MarkdownService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService();
        });

        $this->app->singleton(GeoLookupService::class, function ($app) {
            return new GeoLookupService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.app', 'layouts.admin'], function ($view) {
            $initials = User::where('is_admin', true)
                ->first()
                ?->profile
                ?->avatar_initials ?? 'JF';

            $view->with('siteInitials', $initials);
        });
    }
}
