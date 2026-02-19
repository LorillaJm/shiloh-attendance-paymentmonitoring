<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';
    
    protected static ?string $navigationGroup = 'Overview';
    
    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return 'Command Center';
        }
        
        return 'My Dashboard';
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getSubheading(): ?string
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return 'Optimized real-time overview - cached for performance';
        }
        
        return 'Your personal dashboard';
    }

    public function getWidgets(): array
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Use optimized widgets for production performance
            return [
                \App\Filament\Widgets\OptimizedStatsOverviewWidget::class,
                \App\Filament\Widgets\OptimizedCollectionsTrendChart::class,
                \App\Filament\Widgets\OptimizedAlertsWidget::class,
                \App\Filament\Widgets\OptimizedRecentActivityWidget::class,
            ];
        }
        
        // User dashboard - simplified for attendance encoding
        return [
            \App\Filament\Widgets\UserQuickActionsWidget::class,
            \App\Filament\Widgets\UserAttendanceSummaryWidget::class,
            \App\Filament\Widgets\UserRecentAttendanceWidget::class,
        ];
    }
    
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    \App\Services\DashboardCacheService::clearAll();
                    $this->dispatch('$refresh');
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Dashboard Refreshed')
                        ->body('All data has been reloaded.')
                        ->send();
                }),
        ];
    }
}
