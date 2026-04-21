<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .report-header h1 { margin: 0; font-size: 20px; color: #2563eb; }
        .report-header h2 { margin: 5px 0; font-size: 16px; color: #333; }
        .report-header p { margin: 3px 0; color: #666; font-size: 11px; }
        .report-logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 8px; }
        .summary { background: #f3f4f6; padding: 15px; margin-bottom: 20px; }
        .summary-table { width: 100%; border-collapse: separate; border-spacing: 8px; }
        .summary-table td { padding: 10px; background: white; text-align: center; width: 20%; }
        .summary-label { font-weight: bold; color: #666; font-size: 10px; display: block; margin-bottom: 4px; }
        .summary-value { font-size: 16px; font-weight: bold; display: block; }
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
    @include('reports.partials.header', [
        'reportTitle' => 'Daily Attendance Report',
        'reportSubtitle' => 'Date: ' . \Carbon\Carbon::parse($date)->format('l, F d, Y'),
    ])

    <div class="summary">
        <table class="summary-table">
            <tr>
                <td>
                    <span class="summary-label">TOTAL</span>
                    <span class="summary-value">{{ $summary['total'] }}</span>
                </td>
                <td>
                    <span class="summary-label">PRESENT</span>
                    <span class="summary-value present">{{ $summary['present'] }}</span>
                </td>
                <td>
                    <span class="summary-label">ABSENT</span>
                    <span class="summary-value absent">{{ $summary['absent'] }}</span>
                </td>
                <td>
                    <span class="summary-label">LATE</span>
                    <span class="summary-value late">{{ $summary['late'] }}</span>
                </td>
                <td>
                    <span class="summary-label">EXCUSED</span>
                    <span class="summary-value excused">{{ $summary['excused'] }}</span>
                </td>
            </tr>
        </table>
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
                <td>{{ $record->student?->student_no ?? 'N/A' }}</td>
                <td>{{ $record->student?->full_name ?? 'Unknown Student' }}</td>
                <td>
                    <span class="status-badge status-{{ strtolower($record->status) }}">
                        {{ $record->status }}
                    </span>
                </td>
                <td>{{ $record->remarks ?? '-' }}</td>
                <td>{{ $record->encodedBy?->name ?? '-' }}</td>
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
