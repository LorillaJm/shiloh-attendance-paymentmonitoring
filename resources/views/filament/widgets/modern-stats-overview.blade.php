<div class="modern-stats-grid">
    @foreach($stats as $stat)
    <div class="modern-stat-card" data-color="{{ $stat['label'] }}">
        <div class="stat-content">
            <div class="stat-header">
                <span class="stat-label">{{ $stat['label'] }}</span>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $stat['value'] }}</div>
                @if(isset($stat['description']))
                <div class="stat-description">{{ $stat['description'] }}</div>
                @endif
            </div>
        </div>
        @if(isset($stat['chart']) && count($stat['chart']) > 0)
        <div class="stat-chart">
            <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="gradient-{{ $loop->index }}" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:currentColor;stop-opacity:0.3" />
                        <stop offset="100%" style="stop-color:currentColor;stop-opacity:0" />
                    </linearGradient>
                </defs>
                <polyline
                    fill="url(#gradient-{{ $loop->index }})"
                    stroke="currentColor"
                    stroke-width="2"
                    points="{{ collect($stat['chart'])->map(fn($value, $index) => ($index * (100 / (count($stat['chart']) - 1))) . ',' . (30 - ($value / max($stat['chart']) * 25)))->join(' ') }} 100,30 0,30"
                />
            </svg>
        </div>
        @endif
        <div class="stat-glow"></div>
    </div>
    @endforeach
</div>

<style>
/* Apple Neo Design System */
.modern-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
    padding: 0.5rem;
}

.modern-stat-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-radius: 24px;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.8);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.dark .modern-stat-card {
    background: rgba(30, 30, 30, 0.7);
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        0 2px 8px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.modern-stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.12),
        0 8px 16px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
}

.dark .modern-stat-card:hover {
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.6),
        0 8px 16px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.stat-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.modern-stat-card:hover .stat-glow {
    opacity: 1;
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-header {
    margin-bottom: 1.25rem;
}

.stat-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
}

.dark .stat-label {
    color: #9ca3af;
}

.stat-body {
    margin-bottom: 1.5rem;
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1;
    background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;
    letter-spacing: -0.02em;
}

.dark .stat-value {
    background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-description {
    font-size: 0.9375rem;
    color: #6b7280;
    font-weight: 500;
    font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Text', 'Segoe UI', sans-serif;
}

.dark .stat-description {
    color: #9ca3af;
}

.stat-chart {
    height: 50px;
    margin-top: 1.5rem;
    position: relative;
    z-index: 1;
}

.stat-chart svg {
    width: 100%;
    height: 100%;
}

/* Color variations */
.modern-stat-card[data-color="Total Students"] .stat-chart {
    color: #3b82f6;
}

.modern-stat-card[data-color="Active Students"] .stat-chart {
    color: #10b981;
}

.modern-stat-card[data-color="Due Today"] .stat-chart {
    color: #f59e0b;
}

.modern-stat-card[data-color="Overdue"] .stat-chart {
    color: #ef4444;
}

.modern-stat-card[data-color="Today"] .stat-chart {
    color: #10b981;
}

.modern-stat-card[data-color="This Month"] .stat-chart {
    color: #06b6d4;
}

.modern-stat-card[data-color="Outstanding"] .stat-chart {
    color: #6b7280;
}

/* Responsive Design */
@media (max-width: 640px) {
    .modern-stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 0;
    }
    
    .modern-stat-card {
        padding: 1.5rem;
        border-radius: 20px;
    }
    
    .stat-value {
        font-size: 2.25rem;
    }
    
    .stat-chart {
        height: 40px;
    }
}

@media (min-width: 768px) and (max-width: 1024px) {
    .modern-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1280px) {
    .modern-stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1536px) {
    .modern-stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-stat-card {
    animation: fadeInUp 0.6s ease-out backwards;
}

.modern-stat-card:nth-child(1) { animation-delay: 0.05s; }
.modern-stat-card:nth-child(2) { animation-delay: 0.1s; }
.modern-stat-card:nth-child(3) { animation-delay: 0.15s; }
.modern-stat-card:nth-child(4) { animation-delay: 0.2s; }
.modern-stat-card:nth-child(5) { animation-delay: 0.25s; }
.modern-stat-card:nth-child(6) { animation-delay: 0.3s; }
.modern-stat-card:nth-child(7) { animation-delay: 0.35s; }
</style>
