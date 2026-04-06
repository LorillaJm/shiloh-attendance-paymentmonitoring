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
        /* Inline Apple Dashboard Styles for immediate effect */
        .apple-hero-header {
            text-align: center;
            padding: 3rem 1rem;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .apple-hero-title {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            background: linear-gradient(90deg, #1d1d1f 0%, #515154 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .apple-hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: #6e6e73;
            font-weight: 400;
            max-width: 600px;
            margin: 0 auto;
        }

        .apple-dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Override Filament stat cards */
        .fi-wi-stats-overview-stat {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06) !important;
            transition: all 0.3s ease-in-out !important;
            min-height: 180px !important;
        }

        .fi-wi-stats-overview-stat:hover {
            transform: translateY(-4px) scale(1.02) !important;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1), 0 0 20px rgba(0, 113, 227, 0.15) !important;
        }

        .fi-wi-stats-overview-stat-value {
            font-size: clamp(2rem, 4vw, 2.5rem) !important;
            font-weight: 700 !important;
            letter-spacing: -0.02em !important;
        }

        .fi-wi-stats-overview-stat-label {
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: #6e6e73 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        .fi-wi-stats-overview-stat-description {
            font-size: 0.875rem !important;
            color: #6e6e73 !important;
        }

        /* Page background */
        .fi-main {
            background: linear-gradient(180deg, #f5f5f7 0%, #ffffff 100%) !important;
            background-attachment: fixed !important;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dark mode support */
        .dark .apple-hero-title {
            background: linear-gradient(90deg, #f5f5f7 0%, #b0b0b5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .dark .fi-wi-stats-overview-stat {
            background: linear-gradient(135deg, rgba(44,44,46,0.9) 0%, rgba(44,44,46,0.7) 100%) !important;
        }

        .dark .fi-main {
            background: linear-gradient(180deg, #1d1d1f 0%, #000000 100%) !important;
        }

        /* Chart Widget Styling */
        .fi-wi-chart {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06) !important;
            padding: 1.5rem !important;
            margin-top: 2rem !important;
        }

        .dark .fi-wi-chart {
            background: linear-gradient(135deg, rgba(44,44,46,0.9) 0%, rgba(44,44,46,0.7) 100%) !important;
        }

        .fi-wi-chart-heading {
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            margin-bottom: 1rem !important;
            color: #1d1d1f !important;
        }

        .dark .fi-wi-chart-heading {
            color: #f5f5f7 !important;
        }
    </style>

    
    <div class="apple-dashboard-container">
        <?php if (isset($component)) { $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widgets','data' => ['widgets' => $this->getWidgets(),'columns' => $this->getColumns()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widgets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['widgets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getWidgets()),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getColumns())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $attributes = $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $component = $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>
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
<?php /**PATH C:\Users\HUAWEI\OneDrive\shiloh-attendance-paymentmonitoring\resources\views/filament/pages/dashboard.blade.php ENDPATH**/ ?>