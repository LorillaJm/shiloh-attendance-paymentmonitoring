<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register custom login response for unified login
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        // Register custom logout response
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function (User $user, string $ability) {
            return $user->isAdmin() ? true : null;
        });

        // Register observers
        \App\Models\Student::observe(\App\Observers\StudentObserver::class);
        \App\Models\PaymentSchedule::observe(\App\Observers\PaymentScheduleObserver::class);
        \App\Models\AttendanceRecord::observe(\App\Observers\AttendanceRecordObserver::class);
        \App\Models\Announcement::observe(\App\Observers\AnnouncementObserver::class);

        // Share theme with all views
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $theme = auth()->user()->theme ?? 'light';
                $view->with('userTheme', $theme);
            }
        });
    }
}
