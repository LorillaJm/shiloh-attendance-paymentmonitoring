<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Services\ActivityLogger;
use App\Events\AttendanceUpdated;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class DailyAttendanceEncoder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static string $view = 'filament.pages.daily-attendance-encoder';

    protected static ?string $navigationGroup = 'Attendance Management';

    protected static ?string $navigationLabel = 'Daily Encoder';

    protected static ?int $navigationSort = 1;
    
    protected static ?string $title = 'Daily Attendance Encoder';

    public ?array $data = [];
    public $students = [];
    public $attendanceData = [];

    public function mount(): void
    {
        $this->form->fill([
            'attendance_date' => now()->format('Y-m-d'),
            'status_filter' => 'ACTIVE',
            'search' => '',
        ]);

        $this->loadStudents();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Date & Filters')
                    ->schema([
                        Forms\Components\DatePicker::make('attendance_date')
                            ->label('Attendance Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->maxDate(now())
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->loadStudents()),

                        Forms\Components\Select::make('status_filter')
                            ->label('Student Status')
                            ->options([
                                'ACTIVE' => 'Active Only',
                                'ALL' => 'All Students',
                            ])
                            ->default('ACTIVE')
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->loadStudents()),

                        Forms\Components\TextInput::make('search')
                            ->label('Search Student')
                            ->placeholder('Search by name or student number')
                            ->reactive()
                            ->debounce(500)
                            ->afterStateUpdated(fn () => $this->loadStudents()),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function loadStudents(): void
    {
        $query = Student::query();

        // Filter by status
        if ($this->data['status_filter'] === 'ACTIVE') {
            $query->where('status', 'ACTIVE');
        }

        // Search filter
        if (!empty($this->data['search'])) {
            $search = $this->data['search'];
            $query->where(function ($q) use ($search) {
                $q->where('student_no', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $this->students = $query->orderBy('student_no')->limit(100)->get();

        // Notify if there are more students
        $totalCount = Student::query()
            ->when($this->data['status_filter'] === 'ACTIVE', fn($q) => $q->where('status', 'ACTIVE'))
            ->count();
            
        if ($totalCount > 100) {
            \Filament\Notifications\Notification::make()
                ->info()
                ->title('Showing First 100 Students')
                ->body("Total: {$totalCount} students. Use search to find specific students.")
                ->send();
        }

        // Load existing attendance for the selected date
        $attendanceDate = $this->data['attendance_date'] ?? now()->format('Y-m-d');
        $existingAttendance = AttendanceRecord::where('attendance_date', $attendanceDate)
            ->whereIn('student_id', $this->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        // Initialize attendance data
        $this->attendanceData = [];
        foreach ($this->students as $student) {
            $existing = $existingAttendance->get($student->id);
            $this->attendanceData[$student->id] = [
                'status' => $existing?->status ?? 'PRESENT',
                'remarks' => $existing?->remarks ?? '',
                'exists' => $existing !== null,
                'record_id' => $existing?->id,
            ];
        }
    }

    public function updateStatus($studentId, $status): void
    {
        $this->attendanceData[$studentId]['status'] = $status;
    }

    public function updateRemarks($studentId, $remarks): void
    {
        $this->attendanceData[$studentId]['remarks'] = $remarks;
    }

    public function saveAttendance(): void
    {
        $user = Auth::user();
        $attendanceDate = $this->data['attendance_date'];
        
        // Check if user can edit this date (edit window enforcement)
        if (!Gate::forUser($user)->allows('editDate', [AttendanceRecord::class, $attendanceDate])) {
            $editWindowDays = config('attendance.edit_window_days', 7);
            
            Notification::make()
                ->danger()
                ->title('Edit Window Exceeded')
                ->body("You can only edit attendance within {$editWindowDays} days. Admins can edit any date.")
                ->send();
            return;
        }
        
        try {
            DB::beginTransaction();

            $savedCount = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($this->attendanceData as $studentId => $data) {
                try {
                    $recordData = [
                        'student_id' => $studentId,
                        'attendance_date' => $attendanceDate,
                        'status' => $data['status'],
                        'remarks' => $data['remarks'],
                        'encoded_by_user_id' => Auth::id(),
                    ];

                    if ($data['exists']) {
                        // Update existing record
                        $record = AttendanceRecord::find($data['record_id']);
                        
                        // Store old values for logging
                        $oldStatus = $record->status;
                        $oldRemarks = $record->remarks;
                        
                        $record->update($recordData);
                        $updatedCount++;
                        
                        // Broadcast attendance update
                        broadcast(new AttendanceUpdated(
                            $record->id,
                            $studentId,
                            $attendanceDate,
                            'updated'
                        ))->toOthers();
                        
                        // Log update if status changed
                        if ($oldStatus !== $data['status']) {
                            ActivityLogger::log(
                                description: "Attendance updated",
                                subject: $record,
                                properties: [
                                    'student_id' => $studentId,
                                    'attendance_date' => $attendanceDate,
                                    'old_status' => $oldStatus,
                                    'new_status' => $data['status'],
                                    'old_remarks' => $oldRemarks,
                                    'new_remarks' => $data['remarks'],
                                    'updated_by' => $user->name,
                                    'is_admin_override' => $user->isAdmin() && !Carbon::parse($attendanceDate)->diffInDays(now(), false) <= config('attendance.edit_window_days', 7),
                                ],
                                logName: 'attendance'
                            );
                        }
                    } else {
                        // Create new record
                        $record = AttendanceRecord::create($recordData);
                        $savedCount++;
                        
                        // Broadcast attendance creation
                        broadcast(new AttendanceUpdated(
                            $record->id,
                            $studentId,
                            $attendanceDate,
                            'created'
                        ))->toOthers();
                        
                        // Log creation
                        ActivityLogger::log(
                            description: "Attendance created",
                            subject: $record,
                            properties: [
                                'student_id' => $studentId,
                                'attendance_date' => $attendanceDate,
                                'status' => $data['status'],
                                'remarks' => $data['remarks'],
                                'created_by' => $user->name,
                            ],
                            logName: 'attendance'
                        );
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle unique constraint violation gracefully
                    if ($e->getCode() === '23000') {
                        $errors[] = "Duplicate attendance record for student ID {$studentId}";
                    } else {
                        throw $e;
                    }
                }
            }

            // Log batch operation
            ActivityLogger::log(
                description: "Batch attendance saved",
                subject: null,
                properties: [
                    'attendance_date' => $attendanceDate,
                    'saved_count' => $savedCount,
                    'updated_count' => $updatedCount,
                    'total_students' => count($this->attendanceData),
                    'user_id' => Auth::id(),
                    'user_name' => $user->name,
                    'is_admin' => $user->isAdmin(),
                ],
                logName: 'attendance'
            );

            DB::commit();

            // Reload students to refresh the data
            $this->loadStudents();

            $message = "Saved: {$savedCount} new, Updated: {$updatedCount} existing records.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            Notification::make()
                ->success()
                ->title('Attendance Saved')
                ->body($message)
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Error Saving Attendance')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendanceData as $studentId => $data) {
            $this->attendanceData[$studentId]['status'] = 'PRESENT';
        }

        Notification::make()
            ->success()
            ->title('All Marked as Present')
            ->body('All students have been marked as present. Click "Save Attendance" to save.')
            ->send();
    }

    public function markAllAbsent(): void
    {
        foreach ($this->attendanceData as $studentId => $data) {
            $this->attendanceData[$studentId]['status'] = 'ABSENT';
        }

        Notification::make()
            ->warning()
            ->title('All Marked as Absent')
            ->body('All students have been marked as absent. Click "Save Attendance" to save.')
            ->send();
    }
}
