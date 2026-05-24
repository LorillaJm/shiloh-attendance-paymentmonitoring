<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Attendance Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 30px;
            line-height: 1.4;
        }

        /* Header */
        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
            margin-bottom: 20px;
        }
        .header img { height: 65px; margin-bottom: 8px; }
        .header h1 { font-size: 18px; color: #1e3a5f; margin: 0 0 2px 0; }
        .header h2 { font-size: 14px; color: #2563eb; font-weight: bold; margin: 0 0 2px 0; }
        .header p { font-size: 10px; color: #666666; margin: 2px 0; }

        /* Summary Cards */
        .summary-row { width: 100%; margin-bottom: 20px; }
        .summary-row table { width: 100%; border-collapse: collapse; }
        .summary-row td { width: 20%; text-align: center; padding: 12px 8px; }
        .summary-card {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: center;
        }
        .summary-label { font-size: 9px; font-weight: bold; color: #888888; letter-spacing: 1px; text-transform: uppercase; }
        .summary-value { font-size: 22px; font-weight: bold; margin-top: 4px; }
        .color-total { color: #6366f1; }
        .color-present { color: #10b981; }
        .color-absent { color: #ef4444; }
        .color-late { color: #f59e0b; }
        .color-excused { color: #3b82f6; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th {
            background-color: #2563eb;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .data-table tr.even td { background-color: #f9fafb; }
        .data-table .row-num { width: 30px; text-align: center; color: #999999; }

        /* Status Badges */
        .badge { padding: 3px 8px; font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .badge-present { background-color: #d1fae5; color: #065f46; }
        .badge-absent { background-color: #fee2e2; color: #991b1b; }
        .badge-late { background-color: #fef3c7; color: #92400e; }
        .badge-excused { background-color: #dbeafe; color: #1e40af; }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dddddd;
            text-align: center;
            font-size: 9px;
            color: #999999;
        }

        /* Page break */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    {{-- Header --}}
    @php
        $logoPath = public_path('images/logo.jpg');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo">
        @endif
        <h1>Shiloh's Learning and Development Center</h1>
        <h2>Daily Attendance Report</h2>
        <p>Date: {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</p>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-row">
        <table>
            <tr>
                <td>
                    <div class="summary-card">
                        <div class="summary-label">Total</div>
                        <div class="summary-value color-total">{{ $summary['total'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="summary-label">Present</div>
                        <div class="summary-value color-present">{{ $summary['present'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="summary-label">Absent</div>
                        <div class="summary-value color-absent">{{ $summary['absent'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="summary-label">Late</div>
                        <div class="summary-value color-late">{{ $summary['late'] }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-card">
                        <div class="summary-label">Excused</div>
                        <div class="summary-value color-excused">{{ $summary['excused'] }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Data Table --}}
    @if($records->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="row-num">#</th>
                    <th>Student No</th>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Encoded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $record)
                    <tr class="{{ $index % 2 === 1 ? 'even' : '' }}">
                        <td class="row-num">{{ $index + 1 }}</td>
                        <td>{{ $record->student?->student_no ?? 'N/A' }}</td>
                        <td>{{ $record->student?->full_name ?? 'Unknown' }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($record->status) }}">{{ $record->status }}</span>
                        </td>
                        <td>{{ $record->remarks ?? '-' }}</td>
                        <td>{{ $record->encodedBy?->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 30px; color: #999999;">No attendance records found for this date.</p>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance & Payment System</p>
        <p>Total Records: {{ $records->count() }} | Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
