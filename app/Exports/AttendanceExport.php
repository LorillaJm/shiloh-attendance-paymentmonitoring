<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithDrawings, WithCustomStartCell
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

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
    {
        $logoPath = public_path('images/logo.png');
        if (!file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Shiloh Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for title rows
        $sheet->mergeCells('B1:F1');
        $sheet->setCellValue('B1', "Shiloh's Learning and Development Center");
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setVertical('center');

        $sheet->mergeCells('B2:F2');
        $sheet->setCellValue('B2', 'Attendance Report');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('B3:F3');
        $sheet->setCellValue('B3', 'Period: ' . $this->startDate . ' to ' . $this->endDate);
        $sheet->getStyle('B3')->getFont()->setSize(10)->setItalic(true);

        // Bold header row (row 6 since data starts at A6)
        return [
            6 => ['font' => ['bold' => true]],
        ];
    }
}
