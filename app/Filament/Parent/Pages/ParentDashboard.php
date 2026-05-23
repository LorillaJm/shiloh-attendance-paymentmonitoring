<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Services\ParentPortalService;

class ParentDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-home';
    protected static string $view = 'filament.parent.pages.modern-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'My Children';
    
    protected static bool $shouldRegisterNavigation = true;
    protected static ?int $navigationSort = 1;

    public function getDashboardData()
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return null;
        }

        $service = new ParentPortalService();
        return $service->getDashboardData($guardian);
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
    
    public function getHeading(): string
    {
        return 'My Children';
    }
}
