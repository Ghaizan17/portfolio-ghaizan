<?php

namespace App\Providers;

use App\Models\Profile;
use App\Models\Setting;
use App\Models\SocialLink;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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
        Paginator::defaultView('vendor.pagination.editorial');
        Paginator::defaultSimpleView('vendor.pagination.editorial');

        View::share(rescue(function (): array {
            $settings = Setting::allCached();

            return [
                'siteProfile' => Profile::current(),
                'siteSocialLinks' => SocialLink::query()->active()->get(),
                'siteSettings' => $settings,
                'siteDemoMode' => filter_var($settings['demo_mode'] ?? false, FILTER_VALIDATE_BOOL),
            ];
        }, [
            'siteProfile' => null,
            'siteSocialLinks' => collect(),
            'siteSettings' => [],
            'siteDemoMode' => false,
        ], false));
    }
}
