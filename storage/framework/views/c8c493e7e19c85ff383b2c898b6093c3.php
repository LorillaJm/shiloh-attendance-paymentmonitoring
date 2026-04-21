<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Sheet</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .report-header h1 { margin: 0; font-size: 20px; color: #2563eb; }
        .report-header h2 { margin: 5px 0; font-size: 16px; color: #333; }
        .report-header p { margin: 3px 0; color: #666; font-size: 11px; }
        .report-logo { width: 80px; height: 80px; object-fit: contain; margin-bottom: 8px; }
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
        .signature-section { margin-top: 60px; page-break-inside: avoid; }
        .signature-table { width: 100%; border-collapse: collapse; }
        .signature-cell { width: 33.33%; text-align: center; padding: 0 20px; vertical-align: bottom; }
        .signature-line { border-bottom: 1px solid #333; margin-bottom: 5px; height: 40px; }
        .signature-label { font-size: 11px; font-weight: bold; color: #333; margin-top: 5px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <?php echo $__env->make('reports.partials.header', [
        'reportTitle' => 'Attendance Sheet Report',
        'reportSubtitle' => 'Period: ' . ($filters['start_date'] ?? 'N/A') . ' to ' . ($filters['end_date'] ?? 'N/A'),
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">TOTAL</div>
                <div class="summary-value"><?php echo e($summary['total']); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">PRESENT</div>
                <div class="summary-value present"><?php echo e($summary['present']); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">ABSENT</div>
                <div class="summary-value absent"><?php echo e($summary['absent']); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">LATE</div>
                <div class="summary-value late"><?php echo e($summary['late']); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">EXCUSED</div>
                <div class="summary-value excused"><?php echo e($summary['excused']); ?></div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student No</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Encoded By</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($record->attendance_date->format('Y-m-d')); ?></td>
                <td><?php echo e($record->student?->student_no ?? '-'); ?></td>
                <td><?php echo e($record->student?->full_name ?? '-'); ?></td>
                <td>
                    <span class="status-badge status-<?php echo e(strtolower($record->status)); ?>">
                        <?php echo e($record->status); ?>

                    </span>
                </td>
                <td><?php echo e($record->remarks ?? '-'); ?></td>
                <td><?php echo e($record->encodedBy?->name ?? '-'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Assigned Teacher / Faculty</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Prepared by</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Checked by</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated report from Shiloh Attendance and Payment System</p>
        <p>Printed on <?php echo e(now()->format('F d, Y h:i A')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/reports/attendance-sheet-pdf.blade.php ENDPATH**/ ?>