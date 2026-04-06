<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="space-y-6">
        <?php echo e($this->form); ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Student Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Student Number:</span>
                        <span class="ml-2 font-medium"><?php echo e($student->student_no); ?></span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="ml-2 font-medium"><?php echo e($student->full_name); ?></span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Guardian:</span>
                        <span class="ml-2 font-medium"><?php echo e($student->guardian_name); ?></span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Contact:</span>
                        <span class="ml-2 font-medium"><?php echo e($student->guardian_contact); ?></span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'exportPdf','color' => 'danger','icon' => 'heroicon-o-document-arrow-down']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'exportPdf','color' => 'danger','icon' => 'heroicon-o-document-arrow-down']); ?>
                    Export PDF
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold"><?php echo e($enrollment->package->name); ?></h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Enrolled: <?php echo e($enrollment->enrollment_date->format('F d, Y')); ?>

                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Fee</div>
                            <div class="text-xl font-bold">₱<?php echo e(number_format($enrollment->total_fee, 2)); ?></div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Paid</div>
                            <div class="text-xl font-bold text-green-600">₱<?php echo e(number_format($enrollment->total_paid, 2)); ?></div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Balance</div>
                            <div class="text-xl font-bold text-red-600">₱<?php echo e(number_format($enrollment->remaining_balance_computed, 2)); ?></div>
                        </div>
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2">Installment</th>
                                <th class="text-left py-2">Due Date</th>
                                <th class="text-right py-2">Amount</th>
                                <th class="text-center py-2">Status</th>
                                <th class="text-left py-2">Paid Date</th>
                                <th class="text-left py-2">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $enrollment->paymentSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-2">
                                        <?php echo e($schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}"); ?>

                                    </td>
                                    <td class="py-2"><?php echo e($schedule->due_date ? $schedule->due_date->format('Y-m-d') : '-'); ?></td>
                                    <td class="text-right py-2">₱<?php echo e(number_format($schedule->amount_due, 2)); ?></td>
                                    <td class="text-center py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            <?php echo e($schedule->status === 'PAID' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo e($schedule->status); ?>

                                        </span>
                                    </td>
                                    <td class="py-2"><?php echo e($schedule->paid_at ? $schedule->paid_at->format('Y-m-d') : '-'); ?></td>
                                    <td class="py-2"><?php echo e($schedule->payment_method ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12 text-gray-500">
                Please select a student to view their ledger
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/filament/pages/student-ledger.blade.php ENDPATH**/ ?>