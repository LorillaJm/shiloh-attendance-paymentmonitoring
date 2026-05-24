<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\PaymentSchedule;
use App\Models\Enrollment;

class Notifications extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-bell-alert';
    protected static string $view = 'filament.parent.pages.notifications';
    protected static ?string $navigationLabel = 'Notifications';
    protected static ?string $title = 'Notifications & Reminders';
    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        $count = $user->unreadNotifications()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getNotifications()
    {
        // Get database notifications (announcements) - works even without guardian
        $announcements = Auth::user()->notifications()
            ->whereNull('read_at')
            ->latest()
            ->take(10)
            ->get();

        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return [
                'announcements' => $announcements,
                'overdue_payments' => collect(),
                'upcoming_payments' => collect(),
                'low_sessions' => collect(),
            ];
        }

        $studentIds = $guardian->students->pluck('id');

        // Cache for 5 minutes
        $paymentNotifications = Cache::remember("parent_notifications_{$guardian->id}", 300, function () use ($studentIds) {
            // Overdue payments
            $overduePayments = PaymentSchedule::query()
                ->whereHas('enrollment', function ($q) use ($studentIds) {
                    $q->whereIn('student_id', $studentIds);
                })
                ->where('status', 'OVERDUE')
                ->with(['enrollment.student', 'enrollment.package'])
                ->orderBy('due_date')
                ->get();

            // Upcoming payments (due within 7 days)
            $upcomingPayments = PaymentSchedule::query()
                ->whereHas('enrollment', function ($q) use ($studentIds) {
                    $q->whereIn('student_id', $studentIds);
                })
                ->where('status', 'UNPAID')
                ->whereBetween('due_date', [now(), now()->addDays(7)])
                ->with(['enrollment.student', 'enrollment.package'])
                ->orderBy('due_date')
                ->get();

            // Low session balance (less than 5 sessions remaining)
            $lowSessions = Enrollment::query()
                ->whereIn('student_id', $studentIds)
                ->where('status', 'ACTIVE')
                ->with(['student', 'package'])
                ->get()
                ->filter(function ($enrollment) {
                    if (!$enrollment->total_sessions) {
                        return false;
                    }
                    $remaining = $enrollment->total_sessions - ($enrollment->sessions_used ?? 0);
                    return $remaining > 0 && $remaining <= 5;
                })
                ->map(function ($enrollment) {
                    $enrollment->sessions_remaining = $enrollment->total_sessions - ($enrollment->sessions_used ?? 0);
                    return $enrollment;
                });

            return [
                'overdue_payments' => $overduePayments,
                'upcoming_payments' => $upcomingPayments,
                'low_sessions' => $lowSessions,
            ];
        });

        return [
            'announcements' => $announcements,
            'overdue_payments' => $paymentNotifications['overdue_payments'],
            'upcoming_payments' => $paymentNotifications['upcoming_payments'],
            'low_sessions' => $paymentNotifications['low_sessions'],
        ];
    }

    public function markAsRead($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        $this->dispatch('$refresh');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('$refresh');
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
    
    public function getHeading(): string
    {
        return 'Notifications & Reminders';
    }
}
