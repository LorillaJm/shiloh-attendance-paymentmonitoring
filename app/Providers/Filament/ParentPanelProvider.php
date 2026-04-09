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
            ->brandName('Parent Portal')
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
                'panels::topbar.start',
                fn () => '<div class="flex items-center justify-center flex-1 py-1">
                    <span class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Shiloh Learning Center
                    </span>
                </div>
                <style>
                    /* Hide the X button (close sidebar button) */
                    .fi-topbar-close-sidebar-btn {
                        display: none !important;
                    }
                    
                    /* Hide the hamburger button (open sidebar button) on mobile */
                    .fi-topbar-open-sidebar-btn {
                        display: none !important;
                    }
                </style>'
            )
            ->renderHook(
                'panels::user-menu.before',
                fn () => '<script>
                    // Intercept logout clicks
                    document.addEventListener("click", function(e) {
                        const target = e.target.closest("a[href*=\'/logout\'], button[wire\\\\:click*=\'logout\']");
                        if (target) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            if (confirm("Are you sure you want to log out?")) {
                                // Show success toast
                                const toast = document.createElement("div");
                                toast.innerHTML = `<div style="position:fixed;top:20px;right:20px;z-index:9999;background:#10b981;color:white;padding:16px 24px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);font-weight:500;animation:slideIn 0.3s ease-out">✓ Logout successful</div><style>@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}</style>`;
                                document.body.appendChild(toast);
                                
                                setTimeout(() => {
                                    window.location.href = "/logout";
                                }, 800);
                            }
                        }
                    }, true);
                </script>'
            );
    }
}
