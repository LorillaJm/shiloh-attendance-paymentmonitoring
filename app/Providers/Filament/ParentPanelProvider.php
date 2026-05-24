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
                    50 => '#EBF5FF',
                    100 => '#D6EBFF',
                    200 => '#ADD6FF',
                    300 => '#7ABCFF',
                    400 => '#3399FF',
                    500 => '#0071E3',
                    600 => '#006EDB',
                    700 => '#005AB4',
                    800 => '#00468C',
                    900 => '#003264',
                    950 => '#001E3C',
                ],
                'gray' => [
                    50 => '#FAFAFC',
                    100 => '#F5F5F7',
                    200 => '#EDEDF2',
                    300 => '#D2D2D7',
                    400 => '#AEAEB2',
                    500 => '#6E6E73',
                    600 => '#636366',
                    700 => '#48484A',
                    800 => '#2C2C2E',
                    900 => '#1C1C1E',
                    950 => '#000000',
                ],
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('Parent Portal')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo.png'))
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->maxContentWidth('full')
            ->topNavigation(false)
            ->profile(\App\Filament\Parent\Pages\EditProfile::class)
            ->databaseNotifications()
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
                fn () => '<div class="flex items-center gap-2.5 py-1 min-w-0">
                    <img src="' . asset('images/logo.png') . '" alt="Shiloh Logo" class="h-8 w-8 flex-shrink-0 object-cover rounded-full bg-white shadow-sm p-0.5" />
                    <span class="text-base sm:text-lg md:text-xl font-bold tracking-tight truncate
                        text-gray-900 dark:text-white">
                        Shiloh Learning Center
                    </span>
                </div>
                <style>
                    /* Only hide sidebar buttons on desktop */
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
                <!-- Parent Panel: Responsive Sidebar + Loading Indicator -->
                <style>
                    /* Solid opaque sidebar on mobile */
                    @media (max-width: 1023px) {
                        .fi-sidebar {
                            position: fixed !important;
                            z-index: 50 !important;
                            width: 18rem !important;
                            max-width: 85vw !important;
                            background-color: #ffffff !important;
                            backdrop-filter: none !important;
                        }
                        .dark .fi-sidebar {
                            background-color: #0f172a !important;
                        }
                        .fi-sidebar-header {
                            background-color: #ffffff !important;
                        }
                        .dark .fi-sidebar-header {
                            background-color: #0f172a !important;
                        }
                        .fi-sidebar-nav {
                            background-color: #ffffff !important;
                        }
                        .dark .fi-sidebar-nav {
                            background-color: #0f172a !important;
                        }
                        .fi-sidebar-close-overlay {
                            z-index: 40 !important;
                        }
                        .fi-sidebar-item { min-height: 44px !important; }
                        .fi-sidebar-item-button { cursor: pointer !important; }
                    }
                    /* Topbar z-index */
                    .fi-topbar { z-index: 30 !important; }
                    /* Main content no overflow */
                    .fi-main-ctn, .fi-main { max-width: 100vw !important; overflow-x: hidden !important; }
                    /* Mobile padding */
                    @media (max-width: 639px) {
                        .fi-main { padding: 0.75rem !important; }
                        .fi-header-heading { font-size: 1.25rem !important; }
                    }
                    /* Tables horizontal scroll */
                    .fi-ta { overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
                    @media (max-width: 767px) {
                        .fi-ta-table { font-size: 0.8125rem !important; }
                        .fi-ta-cell, .fi-ta-header-cell { padding: 0.5rem 0.625rem !important; white-space: nowrap !important; }
                        .fi-ta-header-toolbar { flex-wrap: wrap !important; gap: 0.5rem !important; }
                        .fi-ta-search-field { flex: 1 1 100% !important; }
                        .fi-fo-component-ctn { grid-template-columns: 1fr !important; }
                        .fi-btn { min-height: 44px !important; }
                        .fi-input, .fi-select, .fi-textarea, input, select, textarea { min-height: 44px !important; font-size: 1rem !important; }
                        .fi-wi { grid-column: span 1 / span 1 !important; }
                        .fi-widgets { grid-template-columns: 1fr !important; }
                    }
                    /* Modals full-width on mobile */
                    @media (max-width: 639px) {
                        .fi-modal-window { width: calc(100vw - 1rem) !important; max-width: 100% !important; margin: 0.5rem !important; }
                    }
                    /* Tabs scrollable */
                    @media (max-width: 639px) {
                        .fi-tabs-list { overflow-x: auto !important; flex-wrap: nowrap !important; scrollbar-width: none !important; }
                        .fi-tabs-list::-webkit-scrollbar { display: none !important; }
                        .fi-tabs-tab { flex-shrink: 0 !important; white-space: nowrap !important; }
                    }
                    /* Parent dashboard inline-style overrides */
                    @media (max-width: 639px) {
                        [style*="grid-template-columns: repeat(auto-fit, minmax(240px"] {
                            grid-template-columns: 1fr !important;
                            gap: 0.75rem !important;
                        }
                        [style*="grid-template-columns: repeat(auto-fit, minmax(320px"] {
                            grid-template-columns: 1fr !important;
                            gap: 0.75rem !important;
                        }
                        [style*="font-size: 1.875rem"] {
                            font-size: 1.25rem !important;
                        }
                        [style*="padding: 2rem"] {
                            padding: 1.25rem !important;
                        }
                        [style*="padding: 1.5rem"] {
                            padding: 1rem !important;
                        }
                        [style*="width: 80px; height: 80px"][style*="border-radius: 50%"] {
                            width: 56px !important;
                            height: 56px !important;
                            font-size: 1.5rem !important;
                        }
                        [style*="font-size: 1.875rem; font-weight: 700; color: white"] {
                            font-size: 1.25rem !important;
                        }
                    }
                    /* Safe area for iPhone notch */
                    @supports (padding: env(safe-area-inset-bottom)) {
                        .fi-sidebar { padding-bottom: env(safe-area-inset-bottom) !important; }
                        .fi-main { padding-bottom: calc(1rem + env(safe-area-inset-bottom)) !important; }
                    }
                    /* Loading indicator styles */
                    @keyframes parentSpinner { to { transform: rotate(360deg); } }
                    @keyframes parentBarShimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
                    .dark #parent-loading-indicator > div:last-child > div {
                        background: rgba(28,28,30,0.95) !important;
                        border-color: rgba(255,255,255,0.08) !important;
                        box-shadow: 0 8px 24px rgba(0,0,0,0.5) !important;
                    }
                    .dark #parent-loading-indicator > div:last-child > div > span { color: #A1A1A6 !important; }
                    .dark #parent-loading-indicator > div:last-child { background: rgba(0,0,0,0.3) !important; }
                </style>
                <div id="parent-loading-indicator" style="display:none;">
                    <div style="position:fixed;top:0;left:0;right:0;z-index:9999;height:3px;background:rgba(0,0,0,0.05);">
                        <div id="parent-loading-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#0071E3,#3399FF,#0071E3);background-size:200% 100%;border-radius:0 2px 2px 0;transition:width 0.3s ease;animation:parentBarShimmer 1.5s ease infinite;"></div>
                    </div>
                    <div style="position:fixed;inset:0;z-index:9998;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.15);backdrop-filter:blur(1px);">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;padding:24px 32px;border-radius:18px;background:rgba(255,255,255,0.95);box-shadow:0 8px 24px rgba(0,0,0,0.12);border:1px solid #EDEDF2;">
                            <div style="width:36px;height:36px;border:3px solid #EDEDF2;border-top-color:#0071E3;border-radius:50%;animation:parentSpinner 0.7s linear infinite;"></div>
                            <span style="font-size:13px;font-weight:500;color:#6E6E73;">Loading...</span>
                        </div>
                    </div>
                </div>
                <script>
                    (function() {
                        const indicator = document.getElementById("parent-loading-indicator");
                        const bar = document.getElementById("parent-loading-bar");
                        let barInterval = null;
                        function showLoading() {
                            if (!indicator) return;
                            indicator.style.display = "block";
                            let width = 15;
                            bar.style.width = width + "%";
                            clearInterval(barInterval);
                            barInterval = setInterval(() => {
                                if (width < 90) { width += (90 - width) * 0.08; bar.style.width = width + "%"; }
                            }, 150);
                        }
                        function hideLoading() {
                            if (!indicator) return;
                            clearInterval(barInterval);
                            bar.style.width = "100%";
                            setTimeout(() => { indicator.style.display = "none"; bar.style.width = "0%"; }, 250);
                        }
                        document.addEventListener("livewire:navigating", showLoading);
                        document.addEventListener("livewire:navigated", hideLoading);
                        document.addEventListener("click", function(e) {
                            const link = e.target.closest(".fi-sidebar-item-button[href]");
                            if (link && link.getAttribute("href") && !link.getAttribute("href").startsWith("#")) { showLoading(); }
                        });
                        document.addEventListener("livewire:navigating", () => { setTimeout(hideLoading, 15000); });
                    })();
                </script>'
            );
    }
}
