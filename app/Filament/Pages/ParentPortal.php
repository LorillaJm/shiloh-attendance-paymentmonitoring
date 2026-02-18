<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class ParentPortal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.parent-portal';
    protected static ?string $navigationLabel = 'My Children';
    protected static ?int $navigationSort = 1;

    public function getStudents()
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return collect();
        }

        return $guardian->students()->with([
            'enrollments.package',
            'enrollments.paymentSchedules',
            'enrollments.paymentTransactions',
            'sessionOccurrences.sessionType',
            'sessionOccurrences.attendanceRecord',
        ])->get();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isParent();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->isParent();
    }
}
