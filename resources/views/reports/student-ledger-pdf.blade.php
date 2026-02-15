<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Ledger</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #2563eb; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #333; }
        .student-info { background: #f3f4f6; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: bold; width: 150px; color: #666; }
        .enrollment-section { margin-bottom: 30px; page-break-inside: avoid; }
        .enrollment-header { background: #2563eb; color: white; padding: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #6b7280; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .amount { text-align: right; }
        .status-paid { color: #10b981; font-weight: bold; }
        .status-unpaid { color: #ef4444; font-weight: bold; }
        .summary-box { background: #fef3c7; padding: 10px; margin-top: 10px; border-left: 4px solid #f59e0b; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Shiloh Attendance and Payment System</h1>
        <h2>Student Ledger</h2>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="student-info">
        <div class="info-row">
            <div class="info-label">Student Number:</div>
            <div>{{ $student->student_no }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div>{{ $student->full_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Guardian:</div>
            <div>{{ $student->guardian_name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Contact:</div>
            <div>{{ $student->guardian_contact }}</div>
        </div>
    </div>

    @foreach($enrollments as $enrollment)
    <div class="enrollment-section">
        <div class="enrollment-header">
            <strong>{{ $enrollment->package->name }}</strong> - Enrolled: {{ $enrollment->enrollment_date->format('F d, Y') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Installment</th>
                    <th>Due Date</th>
                    <th class="amount">Amount</th>
                    <th>Status</th>
                    <th>Paid Date</th>
                    <th>Method</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollment->paymentSchedules as $schedule)
                <tr>
                    <td>{{ $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}" }}</td>
                    <td>{{ $schedule->due_date ? $schedule->due_date->format('Y-m-d') : '-' }}</td>
                    <td class="amount">₱{{ number_format($schedule->amount_due, 2) }}</td>
                    <td class="{{ $schedule->status === 'PAID' ? 'status-paid' : 'status-unpaid' }}">
                        {{ $schedule->status }}
                    </td>
                    <td>{{ $schedule->paid_at ? $schedule->paid_at->format('Y-m-d') : '-' }}</td>
                    <td>{{ $schedule->payment_method ?? '-' }}</td>
                    <td>{{ $schedule->receipt_no ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-box">
            <strong>Summary:</strong>
            Total Fee: ₱{{ number_format($enrollment->total_fee, 2) }} |
            Total Paid: ₱{{ number_format($enrollment->total_paid, 2) }} |
            Balance: ₱{{ number_format($enrollment->remaining_balance_computed, 2) }}
        </div>
    </div>
    @endforeach

    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance and Payment System</p>
        <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
