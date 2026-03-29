<?php

namespace App\Filament\Parent\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Services\ParentPortalService;

class NotificationBadge extends Widget
{
    protected static string $view = 'filament.parent.widgets.notification-badge';
    
    protected int | string | array $columnSpan = 'full';

    public function getAlertCount(): int
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return 0;
        }

        $service = new ParentPortalService();
        $data = $service->getDashboardData($guardian);
        
        return $data['alerts']['total'] ?? 0;
    }
}
