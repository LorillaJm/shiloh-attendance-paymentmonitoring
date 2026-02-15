<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 24px; color: #2563eb; }
        .header h2 { margin: 5px 0; font-size: 18px; color: #333; }
        .summary { background: #f3f4f6; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
        .summary-item { padding: 10px; background: white; border-radius: 3px; text-align: center; }
        .summary-label { font-weight: bold; color: #666; font-size: 10px; }
        .summary-value { font-size: 16px; font-weight: bold; }
        .present { color: #10b981; }
        .absent { color: #ef4444; }
        .late { color: #f59e0b; }
        .excused { color: #3b82f6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2563eb; color: white; padding: 10px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .status-badge { padding: 4px 8px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .status-present { background: #d1fae5; color: #065f46; }
        .status-absent { background: #fee2e2; color: #991b1b; }
        .status-late { background: #fef3c7; color: #92400e; }
        .status-excused { background: #dbeafe; color: #1e40af; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Shiloh Attendance and Payment System</h1>
        <h2>Daily Attendance Report</h2>
        <p>Date: {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</p>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">TOTAL</div>
                <div class="summary-value">{{ $summary['total'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">PRESENT</div>
                <div class="summary-value present">{{ $summary['present'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">ABSENT</div>
                <div class="summary-value absent">{{ $summary['absent'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">LATE</div>
                <div class="summary-value late">{{ $summary['late'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">EXCUSED</div>
                <div class="summary-value excused">{{ $summary['excused'] }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student No</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Encoded By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->student->student_no }}</td>
                <td>{{ $record->student->full_name }}</td>
                <td>
                    <span class="status-badge status-{{ strtolower($record->status) }}">
                        {{ $record->status }}
                    </span>
                </td>
                <td>{{ $record->remarks ?? '-' }}</td>
                <td>{{ $record->encodedBy->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance and Payment System</p>
        <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
