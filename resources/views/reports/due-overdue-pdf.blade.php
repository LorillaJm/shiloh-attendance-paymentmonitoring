<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Due/Overdue Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #2563eb; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #333; }
        .summary { background: #fef2f2; padding: 15px; margin-bottom: 20px; border-radius: 5px; border-left: 4px solid #ef4444; }
        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .summary-item { padding: 10px; background: white; border-radius: 3px; }
        .summary-label { font-weight: bold; color: #666; font-size: 11px; }
        .summary-value { font-size: 18px; color: #ef4444; font-weight: bold; }
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
    <div class="header">
        <h1>Shiloh Attendance and Payment System</h1>
        <h2>{{ ucfirst($reportType) }} Payments Report</h2>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">TOTAL AMOUNT {{ strtoupper($reportType) }}</div>
                <div class="summary-value">₱{{ number_format($summary['total_amount'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">TOTAL RECORDS</div>
                <div class="summary-value">{{ $summary['total_count'] }}</div>
            </div>
        </div>
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
                <td>{{ $record->enrollment->student->student_no }}</td>
                <td>{{ $record->enrollment->student->full_name }}</td>
                <td>{{ $record->enrollment->package->name }}</td>
                <td>{{ $record->installment_no == 0 ? 'Downpayment' : "Installment #{$record->installment_no}" }}</td>
                <td class="{{ $record->due_date && $record->due_date->isPast() ? 'overdue' : '' }}">
                    {{ $record->due_date ? $record->due_date->format('Y-m-d') : '-' }}
                </td>
                <td class="amount">₱{{ number_format($record->amount_due, 2) }}</td>
                <td>{{ $record->enrollment->student->guardian_contact ?? '-' }}</td>
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
