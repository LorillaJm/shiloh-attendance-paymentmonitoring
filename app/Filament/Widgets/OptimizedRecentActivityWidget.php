<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedRecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.optimized-recent-activity-widget';
    
    protected static ?int $sort = 12; // Last
    
    protected int | string | array $columnSpan = 'full';
    
    // No polling
    protected static ?string $pollingInterval = null;

    public function getRecentPayments(): array
    {
        try {
            return Cache::remember('dashboard_recent_payments_v3', 180, function () {
                // Only get last 10 payments - minimal data
                return DB::table('payment_schedules as ps')
                    ->join('enrollments as e', 'ps.enrollment_id', '=', 'e.id')
                    ->join('students as s', 'e.student_id', '=', 's.id')
                    ->join('packages as p', 'e.package_id', '=', 'p.id')
                    ->select(
                        'ps.paid_at',
                        's.student_no',
                        DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                        'p.name as package_name',
                        'ps.installment_no',
                        'ps.amount_due',
                        'ps.payment_method'
                    )
                    ->where('ps.status', 'PAID')
                    ->whereDate('ps.paid_at', '>=', now('Asia/Manila')->subDays(7))
                    ->orderBy('ps.paid_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->toArray();
            });
        } catch (\Exception $e) {
            \Log::error('OptimizedRecentActivityWidget error: ' . $e->getMessage());
            return [];
        }
    }
}
