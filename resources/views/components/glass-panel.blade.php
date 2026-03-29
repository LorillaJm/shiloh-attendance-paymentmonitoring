{{-- 
    Glassmorphism Panel Component
    Usage: <x-glass-panel>Your content here</x-glass-panel>
--}}

@props([
    'padding' => 'lg',
])

@php
    $paddingClasses = [
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
        'xl' => 'p-10',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'glass-panel ' . ($paddingClasses[$padding] ?? 'p-8')]) }}>
    {{ $slot }}
</div>
