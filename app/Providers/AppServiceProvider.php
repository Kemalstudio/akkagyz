<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Services\Payments\ManualPaymentGateway;
use App\Services\Payments\PaymentGatewayResolver;
use App\Support\ShoppingState;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(ShoppingState::class);

        $this->app->singleton(PaymentGatewayResolver::class, fn () => new PaymentGatewayResolver([
            // Neither method charges anything online yet — both settle
            // manually (cash on delivery / confirmed by an admin). Swap the
            // 'card' entry for a real gateway class here once one is wired up.
            'cash' => new ManualPaymentGateway,
        ]));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');

        // Many views reference $businessSettings (site name, logo, currency)
        // without their controller passing it explicitly — share it
        // everywhere so the brand identity stays in sync across the whole
        // site the moment it's changed in admin settings, and so it never
        // silently renders blank instead of failing loudly.
        View::composer('*', function ($view): void {
            if (! array_key_exists('businessSettings', $view->getData())) {
                $view->with('businessSettings', BusinessSetting::current());
            }
        });

        try {
            $settings = BusinessSetting::current();
        } catch (Throwable) {
            // Infrastructure commands must remain usable during a database
            // outage. Environment mail settings remain the safe fallback.
            return;
        }

        if ($settings->smtp_enabled) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $settings->smtp_host,
                'mail.mailers.smtp.port' => $settings->smtp_port,
                'mail.mailers.smtp.username' => $settings->smtp_username,
                'mail.mailers.smtp.password' => $settings->smtp_password,
                'mail.mailers.smtp.scheme' => $settings->smtp_encryption === 'none' ? null : $settings->smtp_encryption,
                'mail.from.address' => $settings->smtp_from_address,
                'mail.from.name' => $settings->smtp_from_name ?: $settings->site_name,
            ]);
        }
    }
}
