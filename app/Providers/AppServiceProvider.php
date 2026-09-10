<?php

namespace App\Providers;

use App\Http\Controllers\Admin\NotificationController;
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
        View::composer('layouts.admin', function ($view) {
            if (auth('admin')->check()) {
                $notifications = NotificationController::getActiveNotifications();
                $view->with('activeNotifications', $notifications);
            }
        });
    }
}
