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
    <form wire:submit="saveAttendance">
        <?php echo e($this->form); ?>


        <div class="mt-6">
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                 <?php $__env->slot('heading', null, []); ?> 
                    Students (<?php echo e(count($students)); ?>)
                 <?php $__env->endSlot(); ?>

                 <?php $__env->slot('headerEnd', null, []); ?> 
                    <div class="flex flex-wrap gap-2">
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'markAllPresent','color' => 'success','size' => 'sm','type' => 'button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'markAllPresent','color' => 'success','size' => 'sm','type' => 'button']); ?>
                            Mark All Present
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

                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['wire:click' => 'markAllAbsent','color' => 'danger','size' => 'sm','type' => 'button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'markAllAbsent','color' => 'danger','size' => 'sm','type' => 'button']); ?>
                            Mark All Absent
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
                 <?php $__env->endSlot(); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($students) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Student No
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Session
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Remarks
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-100/60 dark:hover:bg-gray-800/60">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <?php echo e($student->student_no); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <?php echo e($student->full_name); ?>

                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap"
                                            x-data="{
                                                confirming: false,
                                                pendingStatus: '',
                                                pendingLabel: '',
                                                currentStatus: <?php echo \Illuminate\Support\Js::from($attendanceData[$student->id]['status'])->toHtml() ?>,
                                                statusColors: {
                                                    'PRESENT': '#16a34a',
                                                    'ABSENT': '#dc2626',
                                                    'LATE': '#d97706',
                                                    'EXCUSED': '#2563eb',
                                                },
                                                requestChange(value, label) {
                                                    if (value === this.currentStatus) return;
                                                    this.pendingStatus = value;
                                                    this.pendingLabel = label;
                                                    this.confirming = true;
                                                },
                                                confirmChange() {
                                                    this.currentStatus = this.pendingStatus;
                                                    $wire.updateStatus(<?php echo e($student->id); ?>, this.pendingStatus);
                                                    this.confirming = false;
                                                    this.pendingStatus = '';
                                                    this.pendingLabel = '';
                                                },
                                                cancelChange() {
                                                    this.confirming = false;
                                                    this.pendingStatus = '';
                                                    this.pendingLabel = '';
                                                }
                                            }"
                                            @status-saved-<?php echo e($student->id); ?>.window="currentStatus = $event.detail.status"
                                        >
                                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                                <?php
                                                    $statusColors = [
                                                        'PRESENT' => ['bg' => '#16a34a', 'border' => '#16a34a'],
                                                        'ABSENT'  => ['bg' => '#dc2626', 'border' => '#dc2626'],
                                                        'LATE'    => ['bg' => '#d97706', 'border' => '#d97706'],
                                                        'EXCUSED' => ['bg' => '#2563eb', 'border' => '#2563eb'],
                                                    ];
                                                ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('attendance.status_options'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <button
                                                        type="button"
                                                        x-on:click="requestChange('<?php echo e($value); ?>', '<?php echo e($label); ?>')"
                                                        class="px-3 py-1 text-xs font-semibold rounded-md border transition-all duration-150"
                                                        x-bind:style="currentStatus === '<?php echo e($value); ?>'
                                                            ? 'background-color:<?php echo e($statusColors[$value]['bg']); ?>;border-color:<?php echo e($statusColors[$value]['border']); ?>;color:#fff;'
                                                            : ''"
                                                        x-bind:class="currentStatus === '<?php echo e($value); ?>'
                                                            ? ''
                                                            : 'border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 bg-transparent'"
                                                    >
                                                        <?php echo e($label); ?>

                                                    </button>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            
                                            <div x-show="confirming" x-cloak x-transition.opacity.duration.150ms
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                                x-on:keydown.escape.window="cancelChange()"
                                            >
                                                <div x-on:click.away="cancelChange()" x-transition.scale.origin.center
                                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 max-w-[90vw] text-center border border-gray-200 dark:border-gray-700"
                                                >
                                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full"
                                                        x-bind:style="'background-color:' + (statusColors[pendingStatus] || '#6b7280') + '20'"
                                                    >
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            x-bind:style="'color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                                        Change Status
                                                    </h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                        Mark <span class="font-semibold text-gray-900 dark:text-white"><?php echo e($student->full_name); ?></span>
                                                        as <span class="font-bold" x-text="pendingLabel"
                                                            x-bind:style="'color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        ></span>?
                                                    </p>
                                                    <div class="flex gap-2 justify-center">
                                                        <button type="button" x-on:click="cancelChange()"
                                                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                            Cancel
                                                        </button>
                                                        <button type="button" x-on:click="confirmChange()"
                                                            class="px-4 py-2 text-sm font-semibold rounded-lg text-white transition-colors"
                                                            x-bind:style="'background-color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        >
                                                            Yes, confirm
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($sessionOccurrenceData[$student->id])): ?>
                                                <?php $sod = $sessionOccurrenceData[$student->id]; ?>
                                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full',
                                                    'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' => $sod['status'] === 'COMPLETED',
                                                    'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100' => $sod['status'] === 'SCHEDULED',
                                                    'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' => $sod['status'] === 'CANCELLED',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' => !in_array($sod['status'], ['COMPLETED', 'SCHEDULED', 'CANCELLED']),
                                                ]); ?>">
                                                    <?php echo e($sod['session_type']); ?> &middot; <?php echo e($sod['status']); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 dark:text-gray-500">No session</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="text"
                                                wire:model.blur="attendanceData.<?php echo e($student->id); ?>.remarks"
                                                placeholder="Optional remarks"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                            />
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <button
                                                type="button"
                                                wire:click="saveIndividualAttendance(<?php echo e($student->id); ?>)"
                                                wire:loading.attr="disabled"
                                                wire:target="saveIndividualAttendance(<?php echo e($student->id); ?>)"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 disabled:opacity-50 transition-colors"
                                            >
                                                <svg wire:loading.remove wire:target="saveIndividualAttendance(<?php echo e($student->id); ?>)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <svg wire:loading wire:target="saveIndividualAttendance(<?php echo e($student->id); ?>)" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                                Save
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'lg']); ?>
                            <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-check-circle','class' => 'w-5 h-5 mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-check-circle','class' => 'w-5 h-5 mr-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                            Save Attendance
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
                <?php else: ?>
                    <div class="text-center py-12">
                        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-user-group','class' => 'w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-user-group','class' => 'w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
                        <p class="text-gray-500 dark:text-gray-400">
                            No students found. Try adjusting your filters.
                        </p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
        </div>
    </form>
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
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/filament/pages/daily-attendance-encoder.blade.php ENDPATH**/ ?>