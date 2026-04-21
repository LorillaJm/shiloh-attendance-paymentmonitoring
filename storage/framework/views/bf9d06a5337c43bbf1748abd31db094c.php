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
    <style>
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pulse-ring { 0% { transform: scale(0.9); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
        .animate-fade-in-up { animation: fadeInUp 0.4s ease-out both; }
        .animate-fade-in-up-1 { animation: fadeInUp 0.4s ease-out 0.05s both; }
        .animate-fade-in-up-2 { animation: fadeInUp 0.4s ease-out 0.1s both; }
        .animate-fade-in-up-3 { animation: fadeInUp 0.4s ease-out 0.15s both; }
        .animate-fade-in-up-4 { animation: fadeInUp 0.4s ease-out 0.2s both; }
        .backup-hero-gradient { background: linear-gradient(135deg, #0d9488 0%, #0891b2 50%, #6366f1 100%); }
        .dark .backup-hero-gradient { background: linear-gradient(135deg, #134e4a 0%, #164e63 50%, #312e81 100%); }
        .stat-card { transition: all 0.2s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1), 0 4px 10px -5px rgba(0,0,0,0.04); }
        .dark .stat-card:hover { box-shadow: 0 8px 25px -5px rgba(0,0,0,0.3); }
        .backup-row { transition: all 0.15s ease; }
        .backup-row:hover { background: rgba(13, 148, 136, 0.04); }
        .dark .backup-row:hover { background: rgba(13, 148, 136, 0.08); }
    </style>

    <div class="space-y-6">

        
        <div class="animate-fade-in-up backup-hero-gradient rounded-2xl p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 400 200" fill="none">
                    <circle cx="350" cy="30" r="80" fill="white" opacity="0.1"/>
                    <circle cx="50" cy="170" r="60" fill="white" opacity="0.08"/>
                    <circle cx="200" cy="100" r="120" fill="white" opacity="0.05"/>
                </svg>
            </div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-circle-stack'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6 text-white']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Database Backup</h2>
                        <p class="mt-1 text-sm text-white/70">
                            Export all <?php echo e($this->backupConfig['tables_count']); ?> tables to Excel files in
                            <code class="text-xs bg-white/15 backdrop-blur-sm px-1.5 py-0.5 rounded font-mono"><?php echo e($this->backupConfig['path']); ?></code>
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <button
                        wire:click="runBackupNow"
                        wire:loading.attr="disabled"
                        wire:target="runBackupNow"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-teal-700 font-semibold text-sm rounded-xl shadow-lg shadow-black/10 hover:bg-gray-50 hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="runBackupNow" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                            </svg>
                            Run Backup Now
                        </span>
                        <span wire:loading wire:target="runBackupNow" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            Backing up...
                        </span>
                    </button>
                </div>
            </div>

            
            <div wire:loading wire:target="runBackupNow" class="mt-4">
                <div class="h-1.5 w-full bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white/80 rounded-full" style="animation: shimmer 1.5s ease-in-out infinite; background-size: 200% 100%; background-image: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent); width: 100%;"></div>
                </div>
                <p class="mt-2 text-xs text-white/60">Exporting tables... This may take a moment.</p>
            </div>
        </div>

        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <?php
                $stats = [
                    ['label' => 'Schedule', 'value' => '1st of month', 'sub' => '02:00 AM Manila', 'icon' => 'heroicon-o-clock', 'color' => 'teal', 'delay' => '1'],
                    ['label' => 'Tables', 'value' => $this->backupConfig['tables_count'], 'sub' => 'configured', 'icon' => 'heroicon-o-table-cells', 'color' => 'cyan', 'delay' => '2'],
                    ['label' => 'Retention', 'value' => $this->backupConfig['retention_months'], 'sub' => 'months kept', 'icon' => 'heroicon-o-archive-box', 'color' => 'indigo', 'delay' => '3'],
                    ['label' => 'Backups', 'value' => count($this->backups), 'sub' => 'total stored', 'icon' => 'heroicon-o-folder-open', 'color' => 'violet', 'delay' => '4'],
                ];
                $colorMap = [
                    'teal' => ['bg' => 'bg-teal-50 dark:bg-teal-900/20', 'icon' => 'text-teal-600 dark:text-teal-400', 'ring' => 'ring-teal-100 dark:ring-teal-800/30'],
                    'cyan' => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'icon' => 'text-cyan-600 dark:text-cyan-400', 'ring' => 'ring-cyan-100 dark:ring-cyan-800/30'],
                    'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'icon' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-100 dark:ring-indigo-800/30'],
                    'violet' => ['bg' => 'bg-violet-50 dark:bg-violet-900/20', 'icon' => 'text-violet-600 dark:text-violet-400', 'ring' => 'ring-violet-100 dark:ring-violet-800/30'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $c = $colorMap[$stat['color']]; ?>
                <div class="animate-fade-in-up-<?php echo e($stat['delay']); ?> stat-card rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-4 sm:p-5">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest"><?php echo e($stat['label']); ?></p>
                            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white tracking-tight"><?php echo e($stat['value']); ?></p>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"><?php echo e($stat['sub']); ?></p>
                        </div>
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg <?php echo e($c['bg']); ?> ring-1 <?php echo e($c['ring']); ?> flex items-center justify-center">
                            <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $stat['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 '.e($c['icon']).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="animate-fade-in-up rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-clock'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-500 dark:text-gray-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Backup History</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo e(count($this->backups)); ?> backup<?php echo e(count($this->backups) !== 1 ? 's' : ''); ?> found</p>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->backups) === 0): ?>
                <div class="p-12 text-center">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-800/50 flex items-center justify-center mb-4 ring-1 ring-gray-100 dark:ring-gray-700">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-circle-stack'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-gray-300 dark:text-gray-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">No backups yet</p>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Click "Run Backup Now" to create your first backup.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-gray-800/30">
                                <th class="text-left px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Period</th>
                                <th class="text-left px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</th>
                                <th class="text-left px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Tables</th>
                                <th class="text-left px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Rows</th>
                                <th class="text-left px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Completed</th>
                                <th class="text-right px-5 sm:px-6 py-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $statusConfig = [
                                        'success' => ['dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Completed'],
                                        'partial' => ['dot' => 'bg-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Partial'],
                                        'failed'  => ['dot' => 'bg-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Failed'],
                                        'running' => ['dot' => 'bg-blue-500 animate-pulse', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Running'],
                                        'unknown' => ['dot' => 'bg-gray-400', 'bg' => 'bg-gray-50 dark:bg-gray-800', 'text' => 'text-gray-600 dark:text-gray-400', 'label' => 'Unknown'],
                                    ];
                                    $s = $statusConfig[$backup['status']] ?? $statusConfig['unknown'];
                                ?>
                                <tr class="backup-row group">
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-calendar-days'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-500 dark:text-gray-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($backup['folder']); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($s['bg']); ?> <?php echo e($s['text']); ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?php echo e($s['dot']); ?>"></span>
                                            <?php echo e($s['label']); ?>

                                        </span>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($backup['total_tables']); ?></span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-0.5">tables</span>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e(number_format($backup['total_rows'] ?? 0)); ?></span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-0.5">rows</span>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($backup['started_at']): ?>
                                            <p class="text-xs font-medium text-gray-600 dark:text-gray-300"><?php echo e(\Carbon\Carbon::parse($backup['started_at'])->format('M d, Y')); ?></p>
                                            <p class="text-[11px] text-gray-400 dark:text-gray-500"><?php echo e(\Carbon\Carbon::parse($backup['started_at'])->format('h:i A')); ?></p>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td class="px-5 sm:px-6 py-3.5 text-right">
                                        <button
                                            wire:click="deleteBackup('<?php echo e($backup['folder']); ?>')"
                                            wire:confirm="Are you sure you want to permanently delete the <?php echo e($backup['folder']); ?> backup? This action cannot be undone."
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 bg-transparent hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 rounded-lg transition-all duration-150 opacity-0 group-hover:opacity-100"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($backup['errors'])): ?>
                                    <tr>
                                        <td colspan="6" class="px-5 sm:px-6 py-3 bg-red-50/50 dark:bg-red-900/5 border-l-2 border-red-400 dark:border-red-500">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-xs font-semibold text-red-700 dark:text-red-400"><?php echo e(count($backup['errors'])); ?> error<?php echo e(count($backup['errors']) > 1 ? 's' : ''); ?> occurred</p>
                                                    <ul class="mt-1 space-y-0.5">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $backup['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="text-[11px] text-red-600 dark:text-red-400/80"><?php echo e($error); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div x-data="{ open: false }" class="animate-fade-in-up rounded-xl overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10">
            <button
                @click="open = !open"
                class="w-full flex items-center justify-between px-5 sm:px-6 py-4 bg-gradient-to-r from-slate-50 to-gray-50 dark:from-gray-800/50 dark:to-gray-900 hover:from-slate-100 hover:to-gray-100 dark:hover:from-gray-800 dark:hover:to-gray-800/80 transition-colors"
            >
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-information-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-blue-600 dark:text-blue-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    </div>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">How it works</span>
                </div>
                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-white/5">
                <div class="px-5 sm:px-6 py-5 grid sm:grid-cols-2 gap-4">
                    <?php
                        $infoItems = [
                            ['icon' => 'heroicon-o-calendar', 'title' => 'Automatic Schedule', 'desc' => 'Runs on the 1st of every month at 2:00 AM (Manila time).'],
                            ['icon' => 'heroicon-o-document-arrow-down', 'title' => 'Excel Export', 'desc' => 'Each table exported as .xlsx with bold headers and auto-sized columns.'],
                            ['icon' => 'heroicon-o-folder', 'title' => 'Storage Location', 'desc' => 'Saved to ' . $this->backupConfig['path'] . '/YYYY-MM/ in project root.'],
                            ['icon' => 'heroicon-o-document-text', 'title' => 'Summary Files', 'desc' => 'backup-summary.json and backup-log.txt generated per run.'],
                            ['icon' => 'heroicon-o-trash', 'title' => 'Auto Cleanup', 'desc' => 'Backups older than ' . $this->backupConfig['retention_months'] . ' months removed automatically.'],
                            ['icon' => 'heroicon-o-command-line', 'title' => 'Manual CLI', 'desc' => 'php artisan backup:run'],
                        ];
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $infoItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="w-7 h-7 rounded-md bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $item['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3.5 h-3.5 text-blue-500 dark:text-blue-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300"><?php echo e($item['title']); ?></p>
                                <p class="mt-0.5 text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed"><?php echo e($item['desc']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

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
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/filament/pages/backup-management.blade.php ENDPATH**/ ?>