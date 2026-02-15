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
            return 'Real-time overview of students, payments, and attendance';
        }
        
        return 'Your personal dashboard';
    }

    public function getWidgets(): array
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return [
                \App\Filament\Widgets\StatsOverviewWidget::class,
                \App\Filament\Widgets\CollectionsTrendChart::class,
                \App\Filament\Widgets\PaymentsDueTrendChart::class,
                \App\Filament\Widgets\DueNextTable::class,
                \App\Filament\Widgets\OverdueTable::class,
                \App\Filament\Widgets\RecentPaymentsTable::class,
                \App\Filament\Widgets\AttendanceSnapshotWidget::class,
            ];
        }
        
        // User dashboard - simplified for attendance encoding
        return [
            \App\Filament\Widgets\UserQuickActionsWidget::class,
            \App\Filament\Widgets\UserAttendanceSummaryWidget::class,
            \App\Filament\Widgets\UserRecentAttendanceWidget::class,
        ];
    }
}
