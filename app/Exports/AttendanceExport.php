<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return AttendanceRecord::with(['student', 'encodedBy'])
            ->whereBetween('attendance_date', [$this->startDate, $this->endDate])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('student_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Student No',
            'Student Name',
            'Status',
            'Remarks',
            'Encoded By',
        ];
    }

    public function map($record): array
    {
        return [
            $record->attendance_date->format('Y-m-d'),
            $record->student->student_no,
            $record->student->full_name,
            $record->status,
            $record->remarks ?? '-',
            $record->encodedBy->name,
        ];
    }

    public function title(): string
    {
        return 'Attendance Records';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
