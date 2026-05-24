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
        $data = $this->getDashboardData();
    ?>

    
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/parent-theme.css')); ?>">
    <?php $__env->stopPush(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$data || $data['students']->isEmpty()): ?>
        
        <div class="flex items-center justify-center" style="min-height: 60vh;">
            <div class="text-center" style="max-width: 400px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">No Students Linked</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.5rem;">No students are currently linked to your account. Please contact the administrator to link your children.</p>
                <a href="mailto:admin@shiloh.local" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; transition: transform 0.2s;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Administrator
                </a>
            </div>
        </div>
    <?php else: ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['alerts']['total'] > 0): ?>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <div style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <p style="color: white; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">You have <?php echo e($data['alerts']['total']); ?> notification(s) requiring attention</p>
                    <p style="color: rgba(255, 255, 255, 0.9); font-size: 0.75rem;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['alerts']['overdue_payments'] > 0): ?>
                            <?php echo e($data['alerts']['overdue_payments']); ?> overdue payment(s)
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['alerts']['upcoming_payments'] > 0): ?>
                            <?php echo e($data['alerts']['upcoming_payments']); ?> upcoming payment(s)
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['alerts']['low_sessions'] > 0): ?>
                            <?php echo e($data['alerts']['low_sessions']); ?> low session alert(s)
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>
                <a href="<?php echo e(route('filament.parent.pages.notifications')); ?>" style="padding: 0.625rem 1.25rem; background: white; color: #f5576c; border-radius: 10px; font-weight: 600; font-size: 0.875rem; text-decoration: none; transition: transform 0.2s; flex-shrink: 0;">
                    View All
                </a>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $data['students']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="margin-bottom: 2rem;">
                
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
                    <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                        <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; flex-shrink: 0; backdrop-filter: blur(10px);">
                            <?php echo e(substr($student->first_name, 0, 1)); ?>

                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <h2 style="font-size: 1.875rem; font-weight: 700; color: white; margin-bottom: 0.5rem;"><?php echo e($student->full_name); ?></h2>
                            <p style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 0.25rem;"><?php echo e($student->student_no); ?></p>
                            <p style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.8);"><?php echo e($student->package_name); ?></p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="padding: 0.5rem 1rem; background: <?php echo e($student->status === 'ACTIVE' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(107, 114, 128, 0.2)'); ?>; color: white; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; backdrop-filter: blur(10px);">
                                <?php echo e($student->status); ?>

                            </span>
                        </div>
                    </div>
                </div>

                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                    
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Today's Status</p>
                        <p style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;">
                            <?php echo e($student->today_attendance?->status ?? 'Not Marked'); ?>

                        </p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($student->today_attendance?->remarks): ?>
                            <p style="font-size: 0.75rem; color: #9ca3af;"><?php echo e(Str::limit($student->today_attendance->remarks, 40)); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Balance Due</p>
                        <p style="font-size: 1.875rem; font-weight: 700; color: #1f2937;">₱<?php echo e(number_format($student->remaining_balance, 2)); ?></p>
                    </div>

                    
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Paid</p>
                        <p style="font-size: 1.875rem; font-weight: 700; color: #10b981;">₱<?php echo e(number_format($student->total_paid, 2)); ?></p>
                        <p style="font-size: 0.75rem; color: #9ca3af;">of ₱<?php echo e(number_format($student->total_fee, 2)); ?> total fee</p>
                    </div>

                    
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Sessions Left</p>
                        <p style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;"><?php echo e($student->remaining_sessions); ?></p>
                        <p style="font-size: 0.75rem; color: #9ca3af;">of <?php echo e($student->total_sessions); ?> total</p>
                    </div>

                    
                    <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 24px; height: 24px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">This Month</p>
                        <p style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;"><?php echo e($data['summary']['attendance']['present']); ?></p>
                        <p style="font-size: 0.75rem; color: #9ca3af;">Present Days</p>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                <div style="height: 1px; background: linear-gradient(90deg, transparent, #e5e7eb, transparent); margin: 2rem 0;"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
            
            <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: center; justify-content: between; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;">Recent Attendance</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data['recent_attendance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem; background: #f9fafb; border-radius: 12px; transition: all 0.2s;">
                            <div style="flex: 1;">
                                <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;"><?php echo e($record->student->full_name); ?></p>
                                <p style="font-size: 0.75rem; color: #6b7280;"><?php echo e($record->attendance_date->format('M d, Y')); ?></p>
                            </div>
                            <span style="padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; 
                                <?php echo e($record->status === 'PRESENT' ? 'background: #d1fae5; color: #065f46;' : ''); ?>

                                <?php echo e($record->status === 'ABSENT' ? 'background: #fee2e2; color: #991b1b;' : ''); ?>

                                <?php echo e($record->status === 'LATE' ? 'background: #fef3c7; color: #92400e;' : ''); ?>

                                <?php echo e($record->status === 'EXCUSED' ? 'background: #dbeafe; color: #1e40af;' : ''); ?>">
                                <?php echo e($record->status); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p style="text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.875rem;">No records yet</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <a href="<?php echo e(route('filament.parent.pages.my-children-attendance')); ?>" style="display: block; margin-top: 1rem; text-align: center; color: #667eea; font-size: 0.875rem; font-weight: 600; text-decoration: none;">
                    View all attendance →
                </a>
            </div>

            
            <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;">Recent Payments</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data['recent_payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem; background: #f9fafb; border-radius: 12px; transition: all 0.2s;">
                            <div style="flex: 1;">
                                <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;">₱<?php echo e(number_format($payment->amount, 2)); ?></p>
                                <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.125rem;"><?php echo e($payment->enrollment->student->full_name); ?></p>
                                <p style="font-size: 0.75rem; color: #9ca3af;"><?php echo e($payment->transaction_date->format('M d, Y')); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p style="text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.875rem;">No payments yet</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <a href="<?php echo e(route('filament.parent.pages.my-children-payments')); ?>" style="display: block; margin-top: 1rem; text-align: center; color: #667eea; font-size: 0.875rem; font-weight: 600; text-decoration: none;">
                    View all payments →
                </a>
            </div>

            
            <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937;">Upcoming Sessions</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data['upcoming_sessions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="padding: 0.875rem; background: #f9fafb; border-radius: 12px; transition: all 0.2s;">
                            <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin-bottom: 0.25rem;"><?php echo e($session->sessionType->name); ?></p>
                            <p style="font-size: 0.75rem; color: #6b7280; margin-bottom: 0.125rem;"><?php echo e($session->student->full_name); ?></p>
                            <p style="font-size: 0.75rem; color: #9ca3af;">
                                <?php echo e($session->session_date->format('M d, Y')); ?> • 
                                <?php echo e(\Carbon\Carbon::parse($session->start_time)->format('g:i A')); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p style="text-align: center; padding: 2rem; color: #9ca3af; font-size: 0.875rem;">No upcoming sessions</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <a href="<?php echo e(route('filament.parent.pages.my-children-sessions')); ?>" style="display: block; margin-top: 1rem; text-align: center; color: #667eea; font-size: 0.875rem; font-weight: 600; text-decoration: none;">
                    View all sessions →
                </a>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php $__env->startPush('scripts'); ?>
        <script>
            // Add hover effects to cards
            document.querySelectorAll('[style*="transition"]').forEach(el => {
                el.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                });
                el.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\Users\pc1\Downloads\Attendance-Payment\resources\views/filament/parent/pages/modern-dashboard.blade.php ENDPATH**/ ?>