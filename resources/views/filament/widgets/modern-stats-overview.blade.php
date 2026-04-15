@php
    $countCards = array_slice($stats, 0, 4);
    $paymentCards = array_slice($stats, 4);
@endphp

{{-- Row 1: Count metrics — 4 columns --}}
<div class="glass-row glass-row--4">
    @foreach($countCards as $i => $stat)
    <div class="glass-card glass-accent-{{ $stat['accent'] ?? 'blue' }}">
        <div class="glass-card__frost"></div>
        <div class="glass-card__shine"></div>

        <div class="glass-card__content">
            <div class="glass-card__label">{{ $stat['label'] }}</div>
            <div class="glass-card__value">{{ $stat['value'] }}</div>
            <div class="glass-card__desc">{{ $stat['description'] ?? '' }}</div>
        </div>

        @if(isset($stat['chart']) && count($stat['chart']) > 0)
        <div class="glass-card__chart">
            <svg viewBox="0 0 120 36" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="gfill-{{ $i }}" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:currentColor;stop-opacity:0.25" />
                        <stop offset="100%" style="stop-color:currentColor;stop-opacity:0.02" />
                    </linearGradient>
                </defs>
                @php
                    $max = max($stat['chart']);
                    $points = collect($stat['chart'])->map(fn($v, $k) =>
                        round($k * (120 / (count($stat['chart']) - 1)), 2) . ',' . round(34 - ($v / $max * 28), 2)
                    )->join(' ');
                @endphp
                <polyline fill="url(#gfill-{{ $i }})" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="{{ $points }} 120,36 0,36" />
                <polyline fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="{{ $points }}" />
            </svg>
        </div>
        @endif

        <div class="glass-card__glow"></div>
    </div>
    @endforeach
</div>

{{-- Row 2: Payment metrics — 3 columns --}}
@if(count($paymentCards) > 0)
<div class="glass-row glass-row--3">
    @foreach($paymentCards as $j => $stat)
    <div class="glass-card glass-accent-{{ $stat['accent'] ?? 'blue' }}">
        <div class="glass-card__frost"></div>
        <div class="glass-card__shine"></div>

        <div class="glass-card__content">
            <div class="glass-card__label">{{ $stat['label'] }}</div>
            <div class="glass-card__value glass-card__value--mono">{{ $stat['value'] }}</div>
            <div class="glass-card__desc">{{ $stat['description'] ?? '' }}</div>
        </div>

        <div class="glass-card__glow"></div>
    </div>
    @endforeach
</div>
@endif

