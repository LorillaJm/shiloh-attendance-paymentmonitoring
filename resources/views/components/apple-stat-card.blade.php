{{-- 
    Apple-Inspired Stat Card Component
    Usage: <x-apple-stat-card label="Total Students" value="150" description="Registered in system" color="primary" />
--}}

@props([
    'label' => '',
    'value' => '0',
    'description' => '',
    'color' => 'primary',
    'icon' => null,
    'chart' => null,
])

<div class="apple-stat-card {{ $color }} animate-fade-in-up">
    {{-- Card Header --}}
    <div class="apple-stat-header">
        <span class="apple-stat-label">{{ $label }}</span>
        
        @if($icon)
            <div class="apple-stat-icon">
                <x-dynamic-component :component="$icon" class="w-5 h-5" />
            </div>
        @endif
    </div>
    
    {{-- Card Value --}}
    <div class="apple-stat-value">
        {{ $value }}
    </div>
    
    {{-- Card Description --}}
    @if($description)
        <div class="apple-stat-description">
            {{ $description }}
        </div>
    @endif
    
    {{-- Card Chart (Optional) --}}
    @if($chart)
        <div class="apple-stat-chart">
            {{ $chart }}
        </div>
    @endif
</div>
