{{-- 
    Apple-Inspired Hero Header Component
    Usage: <x-apple-hero title="Command Center" subtitle="Your dashboard description" />
--}}

@props([
    'title' => '',
    'subtitle' => '',
])

<div class="apple-hero-header">
    <h1 class="apple-hero-title">
        {{ $title }}
    </h1>
    
    @if($subtitle)
        <p class="apple-hero-subtitle">
            {{ $subtitle }}
        </p>
    @endif
</div>
