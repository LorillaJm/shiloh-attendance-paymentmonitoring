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
        $children = $this->getChildren();
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($children->isEmpty()): ?>
        <div class="flex items-center justify-center" style="min-height: 60vh;">
            <div class="text-center" style="max-width: 400px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">No Children Linked</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary);">Please contact the administrator to link your children to your account.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;" class="dark:bg-gray-800 dark:border-gray-700">
                    
                    <div style="width: 100px; height: 100px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: white;">
                        <?php echo e(substr($child->first_name, 0, 1)); ?>

                    </div>

                    
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;" class="dark:text-white">
                            <?php echo e($child->full_name); ?>

                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;" class="dark:text-gray-400">
                            <?php echo e($child->student_no); ?>

                        </p>
                        <span style="display: inline-block; padding: 0.375rem 0.75rem; background: <?php echo e($child->status === 'ACTIVE' ? '#d1fae5' : '#fee2e2'); ?>; color: <?php echo e($child->status === 'ACTIVE' ? '#065f46' : '#991b1b'); ?>; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-top: 0.5rem;">
                            <?php echo e($child->status); ?>

                        </span>
                    </div>

                    
                    <?php
                        $activeEnrollment = $child->enrollments->first();
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeEnrollment && $activeEnrollment->package): ?>
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem;" class="dark:bg-gray-700">
                            <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;" class="dark:text-gray-400">
                                Program
                            </p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937;" class="dark:text-white">
                                <?php echo e($activeEnrollment->package->name); ?>

                            </p>
                        </div>

                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            
                            <div style="background: #f9fafb; border-radius: 12px; padding: 0.875rem; text-align: center;" class="dark:bg-gray-700">
                                <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;" class="dark:text-white">
                                    <?php echo e($activeEnrollment->total_sessions ?? 0); ?>

                                </p>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">
                                    Total Sessions
                                </p>
                            </div>

                            
                            <div style="background: #f9fafb; border-radius: 12px; padding: 0.875rem; text-align: center;" class="dark:bg-gray-700">
                                <p style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;" class="dark:text-white">
                                    ₱<?php echo e(number_format($activeEnrollment->remaining_balance_computed, 0)); ?>

                                </p>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">
                                    Balance
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center;" class="dark:bg-gray-700">
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">
                                No active enrollment
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
                        <a href="<?php echo e(route('filament.parent.pages.my-children-attendance')); ?>" style="flex: 1; padding: 0.625rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-align: center; text-decoration: none; transition: transform 0.2s;">
                            View Attendance
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\Users\pc1\Downloads\Attendance-Payment\resources\views/filament/parent/pages/my-child.blade.php ENDPATH**/ ?>