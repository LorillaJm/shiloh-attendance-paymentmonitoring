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
        $announcementCount = $notifications['announcements']->count();
        $overdueCount = $notifications['overdue_payments']->count();
        $upcomingCount = $notifications['upcoming_payments']->count();
        $lowSessionsCount = $notifications['low_sessions']->count();
        $totalAlerts = $announcementCount + $overdueCount + $upcomingCount + $lowSessionsCount;
    ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($announcementCount > 0): ?>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="h-5 w-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    Announcements
                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                        <?php echo e($announcementCount); ?> new
                    </span>
                </h3>
                <button wire:click="markAllAsRead" class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                    Mark all as read
                </button>
            </div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $notifications['announcements']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rounded-lg border-l-4 border-primary-500 bg-primary-50 dark:bg-primary-900/20 p-4 relative">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex h-2 w-2 rounded-full bg-red-500"></span>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                        <?php echo e($notification->data['title'] ?? 'Announcement'); ?>

                                    </h4>
                                </div>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    <?php echo e($notification->data['message'] ?? ''); ?>

                                </p>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        From: <?php echo e($notification->data['created_by'] ?? 'Admin'); ?>

                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        <?php echo e($notification->created_at->diffForHumans()); ?>

                                    </span>
                                </div>
                            </div>
                            <button wire:click="markAsRead('<?php echo e($notification->id); ?>')" class="ml-3 flex-shrink-0 text-xs font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors" title="Mark as read">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

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
    <?php elseif($overdueCount + $upcomingCount + $lowSessionsCount > 0): ?>
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
<?php /**PATH C:\Users\pc1\Downloads\Attendance-Payment\resources\views/filament/parent/pages/notifications.blade.php ENDPATH**/ ?>