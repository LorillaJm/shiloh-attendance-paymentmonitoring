<?php

namespace App\Filament\Pages;

use App\Models\AttendanceRecord;
use App\Models\Student;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;

class StudentAttendanceView extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static string $view = 'filament.pages.student-attendance-view';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Student Attendance View';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public ?int $selectedStudentId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Select Student')
                    ->schema([
                        Forms\Components\Select::make('selectedStudentId')
                            ->label('Student')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search): array {
                                return Student::query()
                                    ->where(function ($q) use ($search) {
                                        $q->where('student_no', 'like', "%{$search}%")
                                            ->orWhere('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%");
                                    })
                                    ->orderBy('student_no')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($s) => [$s->id => "{$s->student_no} - {$s->full_name}"])
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $student = Student::find($value);
                                return $student ? "{$student->student_no} - {$student->full_name}" : null;
                            })
                            ->placeholder('Type to search students...')
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->selectedStudentId = $state)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sessionOccurrence.sessionType.name')
                    ->label('Session Type')
                    ->placeholder('General Attendance'),

                Tables\Columns\TextColumn::make('sessionOccurrence.teacher.name')
                    ->label('Teacher / Faculty')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sessionOccurrence.start_time')
                    ->label('Time')
                    ->time('h:i A')
                    ->placeholder('-'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'PRESENT',
                        'danger' => 'ABSENT',
                        'warning' => 'LATE',
                        'info' => 'EXCUSED',
                    ]),

                Tables\Columns\TextColumn::make('remarks')
                    ->limit(40)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('encodedBy.name')
                    ->label('Encoded By')
                    ->toggleable(),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->emptyStateHeading($this->selectedStudentId ? 'No attendance records found' : 'Select a student')
            ->emptyStateDescription($this->selectedStudentId ? 'This student has no attendance records yet.' : 'Use the search above to find a student and view their attendance history.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->striped();
    }

    protected function getTableQuery(): Builder
    {
        $query = AttendanceRecord::query()
            ->with(['sessionOccurrence.sessionType', 'sessionOccurrence.teacher', 'encodedBy']);

        if ($this->selectedStudentId) {
            $query->where('student_id', $this->selectedStudentId);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function getAttendanceSummary(): array
    {
        if (!$this->selectedStudentId) {
            return ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        }

        $summary = AttendanceRecord::query()
            ->where('student_id', $this->selectedStudentId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'PRESENT' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'ABSENT' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'LATE' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'EXCUSED' THEN 1 ELSE 0 END) as excused
            ")
            ->first();

        return [
            'total' => $summary->total ?? 0,
            'present' => $summary->present ?? 0,
            'absent' => $summary->absent ?? 0,
            'late' => $summary->late ?? 0,
            'excused' => $summary->excused ?? 0,
        ];
    }

    public function getSelectedStudent(): ?Student
    {
        return $this->selectedStudentId ? Student::find($this->selectedStudentId) : null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }
}
