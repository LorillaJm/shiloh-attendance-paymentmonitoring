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
            return 'Optimized performance dashboard - cached for speed';
        }
        
        return 'Your personal dashboard';
    }
    
    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    public function getWidgets(): array
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                \Log::warning('Dashboard: No authenticated user');
                return [];
            }
            
            if ($user->isAdmin()) {
                // Optimized admin dashboard with responsive layout
                // Load widgets one by one with error handling
                $widgets = [];
                
                try {
                    $widgets[] = \App\Filament\Widgets\OptimizedStatsOverviewWidget::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: OptimizedStatsOverviewWidget failed - ' . $e->getMessage());
                }
                
                try {
                    $widgets[] = \App\Filament\Widgets\FinancialSummaryWidget::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: FinancialSummaryWidget failed - ' . $e->getMessage());
                }
                
                try {
                    $widgets[] = \App\Filament\Widgets\AttendanceSummaryWidget::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: AttendanceSummaryWidget failed - ' . $e->getMessage());
                }
                
                try {
                    $widgets[] = \App\Filament\Widgets\OptimizedCollectionsTrendChart::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: OptimizedCollectionsTrendChart failed - ' . $e->getMessage());
                }
                
                try {
                    $widgets[] = \App\Filament\Widgets\OptimizedAlertsWidget::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: OptimizedAlertsWidget failed - ' . $e->getMessage());
                }
                
                try {
                    $widgets[] = \App\Filament\Widgets\OptimizedRecentActivityWidget::class;
                } catch (\Exception $e) {
                    \Log::error('Dashboard: OptimizedRecentActivityWidget failed - ' . $e->getMessage());
                }
                
                return $widgets;
            }
            
            // User dashboard - simplified for attendance encoding
            return [
                \App\Filament\Widgets\UserQuickActionsWidget::class,
                \App\Filament\Widgets\UserAttendanceSummaryWidget::class,
                \App\Filament\Widgets\UserRecentAttendanceWidget::class,
            ];
        } catch (\Exception $e) {
            \Log::error('Dashboard getWidgets error: ' . $e->getMessage());
            \Log::error('Dashboard getWidgets trace: ' . $e->getTraceAsString());
            return [];
        }
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
