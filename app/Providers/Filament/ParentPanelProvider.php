<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Http\Middleware\ParentAccessMiddleware;

class ParentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parent')
            ->path('parent')
            ->login(false)  // Disable Filament login
            ->loginRouteSlug('login')  // Redirect to our custom login
            ->colors([
                'primary' => [
                    50 => '#EEF2FF',
                    100 => '#E0E7FF',
                    200 => '#C7D2FE',
                    300 => '#A5B4FC',
                    400 => '#818CF8',
                    500 => '#2563EB',
                    600 => '#4F46E5',
                    700 => '#4338CA',
                    800 => '#3730A3',
                    900 => '#312E81',
                    950 => '#1E1B4B',
                ],
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('Shiloh Learning Center - Parent Portal')
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->maxContentWidth('full')
            ->topNavigation(false)
            ->pages([
                \App\Filament\Parent\Pages\ParentDashboard::class,
                \App\Filament\Parent\Pages\MyChild::class,
                \App\Filament\Parent\Pages\MyChildrenAttendance::class,
                \App\Filament\Parent\Pages\MyChildrenSessions::class,
                \App\Filament\Parent\Pages\MyChildrenPayments::class,
                \App\Filament\Parent\Pages\Notifications::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ThrottleRequests::class . ':60,1',
            ])
            ->authMiddleware([
                Authenticate::class,
                ParentAccessMiddleware::class,
                \App\Http\Middleware\RedirectAfterLogin::class,
            ])
            ->authGuard('web')
            ->loginRouteSlug('../admin/login')  // Redirect to unified login
            ->renderHook(
                'panels::user-menu.before',
                fn () => \Blade::render('@livewire(\'theme-toggle\')')
            )
            ->renderHook(
                'panels::head.start',
                fn () => '<script>
                    const theme = "' . (auth()->check() ? (auth()->user()->theme ?? 'light') : 'light') . '";
                    document.documentElement.dataset.theme = theme;
                    if (theme === "dark") {
                        document.documentElement.classList.add("dark");
                    }
                </script>'
            )
            ->renderHook(
                'panels::styles.before',
                fn () => \Blade::render('@vite(["resources/css/apple-dashboard.css", "resources/css/dark-mode-improvements.css"])')
            )
            ->renderHook(
                'panels::scripts.after',
                fn () => \Blade::render('@vite(["resources/js/apple-dashboard.js", "resources/js/theme-toggle.js"])')
            );
    }
}
