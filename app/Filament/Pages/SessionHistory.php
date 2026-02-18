<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Student;
use App\Models\SessionOccurrence;
use Illuminate\Support\Facades\Auth;

class SessionHistory extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.session-history';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Session History Report';

    public ?int $studentId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('studentId')
                ->label('Student')
                ->options(Student::active()->pluck('full_name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\DatePicker::make('startDate')
                ->label('From Date')
                ->default(now()->subMonth()),
            Forms\Components\DatePicker::make('endDate')
                ->label('To Date')
                ->default(now()),
        ];
    }

    public function getSessions()
    {
        if (!$this->studentId) {
            return collect();
        }

        $query = SessionOccurrence::where('student_id', $this->studentId)
            ->with(['sessionType', 'teacher', 'attendanceRecord']);

        if ($this->startDate) {
            $query->where('session_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('session_date', '<=', $this->endDate);
        }

        return $query->orderBy('session_date', 'desc')->get();
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->isAdmin() || $user->isTeacher();
    }
}
