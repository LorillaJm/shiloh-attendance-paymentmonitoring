<?php

namespace App\Exports;

use App\Models\PaymentSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PaymentCollectionExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithDrawings, WithCustomStartCell
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
        return PaymentSchedule::with(['enrollment.student', 'enrollment.package'])
            ->where('status', 'PAID')
            ->whereBetween('paid_at', [$this->startDate, $this->endDate])
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Student No',
            'Student Name',
            'Package',
            'Installment',
            'Amount',
            'Payment Method',
            'Receipt No',
            'Remarks',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->paid_at->format('Y-m-d H:i'),
            $schedule->enrollment->student->student_no,
            $schedule->enrollment->student->full_name,
            $schedule->enrollment->package->name,
            $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}",
            number_format($schedule->amount_due, 2),
            $schedule->payment_method,
            $schedule->receipt_no ?? '-',
            $schedule->remarks ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Payment Collection';
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
        $sheet->mergeCells('B1:I1');
        $sheet->setCellValue('B1', "Shiloh's Learning and Development Center");
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('B1')->getAlignment()->setVertical('center');

        $sheet->mergeCells('B2:I2');
        $sheet->setCellValue('B2', 'Payment Collection Report');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11);

        $sheet->mergeCells('B3:I3');
        $sheet->setCellValue('B3', 'Period: ' . $this->startDate . ' to ' . $this->endDate);
        $sheet->getStyle('B3')->getFont()->setSize(10)->setItalic(true);

        // Bold header row (row 6 since data starts at A6)
        return [
            6 => ['font' => ['bold' => true]],
        ];
    }
}
