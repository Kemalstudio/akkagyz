<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\BusinessSetting;

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
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.custom');
        $settings = BusinessSetting::current();
        if ($settings->smtp_enabled) {
            config(['mail.default'=>'smtp','mail.mailers.smtp.host'=>$settings->smtp_host,'mail.mailers.smtp.port'=>$settings->smtp_port,'mail.mailers.smtp.username'=>$settings->smtp_username,'mail.mailers.smtp.password'=>$settings->smtp_password,'mail.mailers.smtp.scheme'=>$settings->smtp_encryption==='none'?null:$settings->smtp_encryption,'mail.from.address'=>$settings->smtp_from_address,'mail.from.name'=>$settings->smtp_from_name?:$settings->site_name]);
        }
    }
}
