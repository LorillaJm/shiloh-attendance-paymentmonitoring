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
                    50 => '240, 249, 255',
                    100 => '224, 242, 254',
                    200 => '186, 230, 253',
                    300 => '125, 211, 252',
                    400 => '56, 189, 248',
                    500 => '14, 165, 233',
                    600 => '2, 132, 199',
                    700 => '3, 105, 161',
                    800 => '7, 89, 133',
                    900 => '12, 74, 110',
                    950 => '8, 47, 73',
                ],
                'gray' => [
                    50 => '249, 250, 251',
                    100 => '243, 244, 246',
                    200 => '229, 231, 235',
                    300 => '209, 213, 219',
                    400 => '156, 163, 175',
                    500 => '107, 114, 128',
                    600 => '75, 85, 99',
                    700 => '55, 65, 81',
                    800 => '31, 41, 55',
                    900 => '17, 24, 39',
                    950 => '3, 7, 18',
                ],
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('Admin Panel')
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
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
