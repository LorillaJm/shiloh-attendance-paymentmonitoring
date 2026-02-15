<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserQuickActionsWidget extends Widget
{
    protected static ?int $sort = 2;
    
    protected static string $view = 'filament.widgets.user-quick-actions';
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return !auth()->user()->isAdmin();
    }

    public function getTodayDate(): string
    {
        return now('Asia/Manila')->format('l, F d, Y');
    }
}
