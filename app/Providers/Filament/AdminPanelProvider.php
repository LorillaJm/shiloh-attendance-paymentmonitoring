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
                fn () => '<div class="flex items-center gap-2.5 py-1 min-w-0">
                    <img src="' . asset('images/logo.png') . '" alt="Shiloh Logo" class="h-8 w-8 flex-shrink-0 object-cover rounded-full bg-white shadow-sm p-0.5" />
                    <span class="text-base sm:text-lg md:text-xl font-bold tracking-tight truncate
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
                    /* Ensure hamburger is visible and on the left on mobile */
                    @media (max-width: 1023px) {
                        .fi-topbar-open-sidebar-btn,
                        .fi-topbar-close-sidebar-btn {
                            order: -1 !important;
                            flex-shrink: 0 !important;
                        }
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
            )
            ->renderHook(
                'panels::body.end',
                fn () => '<script>
                    document.addEventListener("livewire:init", () => {
                        Livewire.hook("request", ({ fail }) => {
                            fail(({ status, preventDefault }) => {
                                if (status === 419) {
                                    preventDefault();
                                    if (confirm("Your session has expired. The page will refresh to continue.")) {
                                        window.location.reload();
                                    } else {
                                        window.location.reload();
                                    }
                                }
                                if (status === 500) {
                                    preventDefault();
                                    const toast = document.createElement("div");
                                    toast.innerHTML = \'<div style="position:fixed;top:20px;right:20px;z-index:9999;background:#ef4444;color:white;padding:16px 24px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);font-weight:500;max-width:400px;animation:slideIn 0.3s ease-out">A server error occurred. Refreshing...<br><small>If this persists, please log in again.</small></div><style>@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}</style>\';
                                    document.body.appendChild(toast);
                                    setTimeout(() => window.location.reload(), 2000);
                                }
                                if (status === 503) {
                                    preventDefault();
                                    const toast = document.createElement("div");
                                    toast.innerHTML = \'<div style="position:fixed;top:20px;right:20px;z-index:9999;background:#f59e0b;color:white;padding:16px 24px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);font-weight:500;max-width:400px;animation:slideIn 0.3s ease-out">Temporary connection issue. Retrying...<br><small>Please wait a moment.</small></div><style>@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}</style>\';
                                    document.body.appendChild(toast);
                                    setTimeout(() => window.location.reload(), 3000);
                                }
                            });
                        });
                    });
                </script>'
            )
            ->renderHook(
                'panels::body.end',
                fn () => '
                <!-- Page Navigation Loading Indicator -->
                <div id="page-loading-indicator" style="display:none;">
                    <!-- Top progress bar -->
                    <div style="position:fixed;top:0;left:0;right:0;z-index:9999;height:3px;background:rgba(0,0,0,0.05);">
                        <div id="page-loading-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#14b8a6,#5eead4,#14b8a6);background-size:200% 100%;border-radius:0 2px 2px 0;transition:width 0.3s ease;animation:loadingBarShimmer 1.5s ease infinite;"></div>
                    </div>
                    <!-- Centered spinner overlay -->
                    <div style="position:fixed;inset:0;z-index:9998;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.15);backdrop-filter:blur(1px);-webkit-backdrop-filter:blur(1px);">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:24px 32px;border-radius:16px;background:rgba(255,255,255,0.95);box-shadow:0 8px 32px rgba(0,0,0,0.12);border:1px solid rgba(0,0,0,0.06);">
                            <div style="width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:#14b8a6;border-radius:50%;animation:pageSpinner 0.7s linear infinite;"></div>
                            <span style="font-size:13px;font-weight:500;color:#64748b;letter-spacing:0.01em;">Loading...</span>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes pageSpinner { to { transform: rotate(360deg); } }
                    @keyframes loadingBarShimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
                    .dark #page-loading-indicator > div:last-child > div {
                        background: rgba(15,23,42,0.95) !important;
                        border-color: rgba(255,255,255,0.06) !important;
                        box-shadow: 0 8px 32px rgba(0,0,0,0.4) !important;
                    }
                    .dark #page-loading-indicator > div:last-child > div > span {
                        color: #94a3b8 !important;
                    }
                    .dark #page-loading-indicator > div:last-child {
                        background: rgba(0,0,0,0.25) !important;
                    }
                </style>
                <script>
                    (function() {
                        const indicator = document.getElementById("page-loading-indicator");
                        const bar = document.getElementById("page-loading-bar");
                        let barInterval = null;

                        function showLoading() {
                            if (!indicator) return;
                            indicator.style.display = "block";
                            let width = 15;
                            bar.style.width = width + "%";
                            clearInterval(barInterval);
                            barInterval = setInterval(() => {
                                if (width < 90) {
                                    width += (90 - width) * 0.08;
                                    bar.style.width = width + "%";
                                }
                            }, 150);
                        }

                        function hideLoading() {
                            if (!indicator) return;
                            clearInterval(barInterval);
                            bar.style.width = "100%";
                            setTimeout(() => {
                                indicator.style.display = "none";
                                bar.style.width = "0%";
                            }, 250);
                        }

                        document.addEventListener("livewire:navigating", showLoading);
                        document.addEventListener("livewire:navigated", hideLoading);

                        // Also handle sidebar link clicks for non-Livewire navigation
                        document.addEventListener("click", function(e) {
                            const link = e.target.closest(".fi-sidebar-item-button[href]");
                            if (link && link.getAttribute("href") && !link.getAttribute("href").startsWith("#")) {
                                showLoading();
                            }
                        });

                        // Safety: hide after 15s max
                        document.addEventListener("livewire:navigating", () => {
                            setTimeout(hideLoading, 15000);
                        });
                    })();
                </script>'
            );
    }
}
