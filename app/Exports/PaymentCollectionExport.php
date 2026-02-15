<?php

namespace App\Exports;

use App\Models\PaymentSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentCollectionExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
