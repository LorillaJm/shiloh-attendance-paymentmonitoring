<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Routing\Middleware\ThrottleRequests;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(false)  // Disable Filament login
            ->loginRouteSlug('login')  // Redirect to our custom login
            ->colors([
                'primary' => [
                    50 => '240, 253, 250',
                    100 => '204, 251, 241',
                    200 => '153, 246, 228',
                    300 => '94, 234, 212',
                    400 => '45, 212, 191',
                    500 => '20, 184, 166',
                    600 => '13, 148, 136',
                    700 => '15, 118, 110',
                    800 => '17, 94, 89',
                    900 => '20, 78, 74',
                    950 => '4, 47, 46',
                ],
                'gray' => [
                    50 => '248, 250, 252',
                    100 => '241, 245, 249',
                    200 => '226, 232, 240',
                    300 => '203, 213, 225',
                    400 => '148, 163, 184',
                    500 => '100, 116, 139',
                    600 => '71, 85, 105',
                    700 => '51, 65, 85',
                    800 => '30, 41, 59',
                    900 => '15, 23, 42',
                    950 => '8, 12, 24',
                ],
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('Shiloh Learning Center')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo.png'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ])
            ->authGuard('web')
            ->loginRouteSlug('login')
            ->middleware([
                \App\Http\Middleware\RedirectAfterLogin::class,
            ], isPersistent: true)
            ->navigationGroups([
                'Overview',
                'Master Data',
                'Enrollment Management',
                'User Management',
                'Session Management',
                'Attendance Management',
                'Payment Management',
                'Reports',
                'System',
                'Administration',
                'Payment Monitoring',
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->breadcrumbs(true)
            ->renderHook(
                'panels::topbar.start',
                fn () => '<div class="flex items-center justify-center flex-1 gap-2.5 py-1">
                    <img src="' . asset('images/logo.png') . '" alt="Shiloh Logo" class="h-9 w-9 object-cover rounded-full bg-white shadow-sm p-0.5" />
                    <span class="text-lg sm:text-xl md:text-2xl font-bold tracking-tight truncate
                        text-gray-900 dark:text-gray-100">
                        Shiloh Learning Center
                    </span>
                </div>
                <style>
                    /* Only hide sidebar buttons on desktop where collapse arrow works */
                    @media (min-width: 1024px) {
                        .fi-topbar-close-sidebar-btn { display: none !important; }
                        .fi-topbar-open-sidebar-btn { display: none !important; }
                    }
                </style>'
            )
            ->renderHook(
                'panels::user-menu.before',
                fn () => '<script>
                    document.addEventListener("alpine:init", () => {
                        Alpine.data("logoutConfirmation", () => ({
                            showConfirm: false,
                            showSuccess: false,
                            confirmLogout() {
                                this.showConfirm = true;
                            },
                            proceedLogout() {
                                this.showConfirm = false;
                                this.showSuccess = true;
                                setTimeout(() => {
                                    window.location.href = "/logout";
                                }, 1000);
                            },
                            cancelLogout() {
                                this.showConfirm = false;
                            }
                        }));
                    });
                    
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
