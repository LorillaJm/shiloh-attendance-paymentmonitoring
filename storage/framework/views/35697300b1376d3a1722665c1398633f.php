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
    <?php
        $notifications = $this->getNotifications();
        $overdueCount = $notifications['overdue_payments']->count();
        $upcomingCount = $notifications['upcoming_payments']->count();
        $lowSessionsCount = $notifications['low_sessions']->count();
        $totalAlerts = $overdueCount + $upcomingCount + $lowSessionsCount;
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalAlerts === 0): ?>
        <div class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">All Clear!</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">You have no pending notifications or reminders.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($overdueCount > 0): ?>
                <div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 dark:bg-red-900/20">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-red-800 dark:text-red-300">
                                Overdue Payments (<?php echo e($overdueCount); ?>)
                            </h3>
                            <div class="mt-2 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications['overdue_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo e($payment->enrollment->student->full_name); ?>

                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    <?php echo e($payment->enrollment->package->name ?? 'Package'); ?>

                                                </p>
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                    Due: <?php echo e($payment->due_date->format('M d, Y')); ?> 
                                                    (<?php echo e($payment->due_date->diffForHumans()); ?>)
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-red-600 dark:text-red-400">
                                                    ₱<?php echo e(number_format($payment->amount_due, 2)); ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingCount > 0): ?>
                <div class="rounded-lg border-l-4 border-orange-500 bg-orange-50 p-4 dark:bg-orange-900/20">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-orange-800 dark:text-orange-300">
                                Upcoming Payments (<?php echo e($upcomingCount); ?>)
                            </h3>
                            <div class="mt-2 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications['upcoming_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo e($payment->enrollment->student->full_name); ?>

                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    <?php echo e($payment->enrollment->package->name ?? 'Package'); ?>

                                                </p>
                                                <p class="mt-1 text-xs text-orange-600 dark:text-orange-400">
                                                    Due: <?php echo e($payment->due_date->format('M d, Y')); ?> 
                                                    (<?php echo e($payment->due_date->diffForHumans()); ?>)
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-orange-600 dark:text-orange-400">
                                                    ₱<?php echo e(number_format($payment->amount_due, 2)); ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lowSessionsCount > 0): ?>
                <div class="rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 dark:bg-yellow-900/20">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">
                                Low Session Balance (<?php echo e($lowSessionsCount); ?>)
                            </h3>
                            <div class="mt-2 space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications['low_sessions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="rounded-lg bg-white p-3 dark:bg-gray-800">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo e($enrollment->student->full_name); ?>

                                                </p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                                    <?php echo e($enrollment->package->name ?? 'Package'); ?>

                                                </p>
                                                <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                                    Only <?php echo e($enrollment->sessions_remaining); ?> session(s) remaining
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    <?php echo e($enrollment->sessions_remaining); ?> left
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/filament/parent/pages/notifications.blade.php ENDPATH**/ ?>