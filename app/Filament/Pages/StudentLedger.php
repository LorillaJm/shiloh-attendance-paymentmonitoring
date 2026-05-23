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

    protected static ?string $navigationIcon = 'heroicon-m-document-text';

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
                            ->options(function () {
                                return \App\Models\Student::query()
                                    ->orderBy('student_no')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(fn ($student) => [
                                        $student->id => "{$student->student_no} - {$student->full_name}"
                                    ]);
                            })
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
                            ->getOptionLabelUsing(function ($value): ?string {
                                $student = \App\Models\Student::find($value);
                                return $student ? "{$student->student_no} - {$student->full_name}" : null;
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadStudent())
                            ->placeholder('Select a student'),
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

        try {
            set_time_limit(120);

            $pdf = Pdf::loadView('reports.student-ledger-pdf', [
                'student' => $this->student,
                'enrollments' => $this->enrollments,
            ])->setPaper('a4', 'portrait');

            $filename = 'student-ledger-' . $this->student->student_no . '-' . now()->format('Y-m-d-His') . '.pdf';
            $dir = storage_path('app/temp-reports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents("{$dir}/{$filename}", $pdf->output());

            $url = \Illuminate\Support\Facades\URL::signedRoute('report.download', ['filename' => $filename]);
            $this->js("window.open('{$url}', '_blank')");
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('PDF Export Failed')
                ->body($e->getMessage())
                ->send();
        }
    }
}
