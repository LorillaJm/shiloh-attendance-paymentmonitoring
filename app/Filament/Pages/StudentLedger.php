<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\Enrollment;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;

class StudentLedger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.student-ledger';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Student Ledger';

    protected static ?int $navigationSort = 11;

    public ?array $data = [];
    public $student = null;
    public $enrollments = [];

    public function mount(): void
    {
        $this->form->fill([
            'student_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Select Student')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                \App\Models\Student::where('student_no', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($student) => [
                                        $student->id => "{$student->student_no} - {$student->full_name}"
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                \App\Models\Student::find($value)?->student_no . ' - ' . \App\Models\Student::find($value)?->full_name
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->loadStudent()),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadStudent(): void
    {
        if ($this->data['student_id'] ?? null) {
            $this->student = Student::with([
                'enrollments.package',
                'enrollments.paymentSchedules' => fn ($q) => $q->orderBy('installment_no')
            ])->find($this->data['student_id']);

            $this->enrollments = $this->student->enrollments ?? collect();
        } else {
            $this->student = null;
            $this->enrollments = collect();
        }
    }

    public function exportPdf()
    {
        if (!$this->student) {
            Notification::make()
                ->warning()
                ->title('No Student Selected')
                ->body('Please select a student first.')
                ->send();
            return;
        }

        $pdf = Pdf::loadView('reports.student-ledger-pdf', [
            'student' => $this->student,
            'enrollments' => $this->enrollments,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'student-ledger-' . $this->student->student_no . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
