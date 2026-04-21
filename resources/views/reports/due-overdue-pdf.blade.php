<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Due/Overdue Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .report-header h1 { margin: 0; font-size: 20px; color: #2563eb; }
        .report-header h2 { margin: 5px 0; font-size: 16px; color: #333; }
        .report-header p { margin: 3px 0; color: #666; font-size: 11px; }
        .report-logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 8px; }
        .summary { background: #fef2f2; padding: 15px; margin-bottom: 20px; border-left: 4px solid #ef4444; }
        .summary-table-sm { width: 100%; border-collapse: separate; border-spacing: 8px; }
        .summary-table-sm td { padding: 10px; background: white; width: 50%; }
        .summary-label { font-weight: bold; color: #666; font-size: 11px; display: block; margin-bottom: 4px; }
        .summary-value { font-size: 18px; color: #ef4444; font-weight: bold; display: block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #ef4444; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .amount { text-align: right; }
        .overdue { color: #ef4444; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'reportTitle' => ucfirst($reportType) . ' Payments Report',
    ])

    <div class="summary">
        <table class="summary-table-sm">
            <tr>
                <td>
                    <span class="summary-label">TOTAL AMOUNT {{ strtoupper($reportType) }}</span>
                    <span class="summary-value">₱{{ number_format($summary['total_amount'], 2) }}</span>
                </td>
                <td>
                    <span class="summary-label">TOTAL RECORDS</span>
                    <span class="summary-value">{{ $summary['total_count'] }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student No</th>
                <th>Student Name</th>
                <th>Package</th>
                <th>Installment</th>
                <th>Due Date</th>
                <th class="amount">Amount</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->enrollment?->student?->student_no ?? '-' }}</td>
                <td>{{ $record->enrollment?->student?->full_name ?? '-' }}</td>
                <td>{{ $record->enrollment?->package?->name ?? '-' }}</td>
                <td>{{ $record->installment_no == 0 ? 'Downpayment' : "Installment #{$record->installment_no}" }}</td>
                <td class="{{ $record->due_date && $record->due_date->isPast() ? 'overdue' : '' }}">
                    {{ $record->due_date ? $record->due_date->format('Y-m-d') : '-' }}
                </td>
                <td class="amount">₱{{ number_format($record->amount_due, 2) }}</td>
                <td>{{ $record->enrollment?->student?->guardian_contact ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right;">TOTAL:</th>
                <th class="amount">₱{{ number_format($summary['total_amount'], 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance and Payment System</p>
        <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
