<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Collection Report</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .report-header h1 {
            margin: 0;
            font-size: 16px;
            color: #2563eb;
        }
        .report-header h2 {
            margin: 5px 0;
            font-size: 12px;
            color: #333;
        }
        .report-header p {
            margin: 3px 0;
            color: #666;
            font-size: 9px;
        }
        .report-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 5px;
        }
        .summary {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-table-sm {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table-sm td {
            padding: 8px;
            background: white;
            width: 50%;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
            font-size: 9px;
            display: block;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 14px;
            color: #2563eb;
            font-weight: bold;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
        }
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8px;
            word-wrap: break-word;
            overflow: hidden;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
        .amount {
            text-align: right;
        }
        /* Column widths */
        .col-date { width: 10%; }
        .col-student-no { width: 9%; }
        .col-student-name { width: 18%; }
        .col-package { width: 20%; }
        .col-installment { width: 13%; }
        .col-amount { width: 12%; }
        .col-method { width: 12%; }
        .col-receipt { width: 6%; }
    </style>
</head>
<body>
    @include('reports.partials.header', [
        'reportTitle' => 'Payment Collection Report',
        'reportSubtitle' => 'Period: ' . ($filters['start_date'] ?? 'N/A') . ' to ' . ($filters['end_date'] ?? 'N/A'),
    ])

    <div class="summary">
        <table class="summary-table-sm">
            <tr>
                <td>
                    <span class="summary-label">TOTAL COLLECTIONS</span>
                    <span class="summary-value">₱{{ number_format($summary['total_amount'], 2) }}</span>
                </td>
                <td>
                    <span class="summary-label">TOTAL TRANSACTIONS</span>
                    <span class="summary-value">{{ $summary['total_count'] }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-date">Date</th>
                <th class="col-student-no">Student No</th>
                <th class="col-student-name">Student Name</th>
                <th class="col-package">Package</th>
                <th class="col-installment">Installment</th>
                <th class="col-amount amount">Amount</th>
                <th class="col-method">Method</th>
                <th class="col-receipt">Receipt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td class="col-date">{{ $record->paid_at->format('Y-m-d H:i') }}</td>
                <td class="col-student-no">{{ $record->enrollment?->student?->student_no ?? '-' }}</td>
                <td class="col-student-name">{{ $record->enrollment?->student?->full_name ?? '-' }}</td>
                <td class="col-package">{{ Str::limit($record->enrollment?->package?->name ?? '-', 30) }}</td>
                <td class="col-installment">{{ $record->installment_no == 0 ? 'Downpayment' : "Installment #{$record->installment_no}" }}</td>
                <td class="col-amount amount">₱{{ number_format($record->amount_due, 2) }}</td>
                <td class="col-method">{{ str_replace('_', ' ', $record->payment_method) }}</td>
                <td class="col-receipt">{{ $record->receipt_no ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right; padding-right: 8px;">TOTAL:</th>
                <th class="amount">₱{{ number_format($summary['total_amount'], 2) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance and Payment System</p>
        <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
