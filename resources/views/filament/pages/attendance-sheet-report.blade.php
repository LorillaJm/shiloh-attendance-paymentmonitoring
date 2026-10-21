<x-filament-panels::page>
    <!-- Force browser to reload with cache buster -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <style>
        /* Professional Attendance Cards - Dashboard Style v3.0 */
        .attendance-stats-card {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0.875rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            padding: 1.25rem !important;
            position: relative !important;
            overflow: hidden !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            min-height: 130px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
        }
        
        .attendance-stats-card::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 100%) !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
        }
        
        .attendance-stats-card:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.3) !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        
        .attendance-stats-card:hover::before {
            opacity: 1 !important;
        }
        
        /* Light mode override */
        html:not(.dark) .attendance-stats-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%) !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
        }
        
        html:not(.dark) .attendance-stats-card:hover {
            border-color: rgba(0, 0, 0, 0.12) !important;
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Card header with icon and badge */
        .attendance-card-header {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            margin-bottom: 0.75rem !important;
        }
        
        /* Icon container */
        .attendance-stats-icon-bg {
            width: 2.5rem !important;
            height: 2.5rem !important;
            border-radius: 0.625rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .attendance-stats-icon-bg::before {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            background: inherit !important;
            opacity: 0.8 !important;
        }
        
        .attendance-stats-icon-bg svg {
            width: 1.25rem !important;
            height: 1.25rem !important;
            position: relative !important;
            z-index: 1 !important;
        }
        
        .attendance-stats-card:hover .attendance-stats-icon-bg {
            transform: scale(1.08) !important;
        }
        
        /* Percentage badge */
        .attendance-percentage-badge {
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            padding: 0.3rem 0.6rem !important;
            border-radius: 0.5rem !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Card labels */
        .attendance-stats-label {
            font-size: 0.6875rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
            color: #9ca3af !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.2 !important;
            opacity: 0.9 !important;
        }
        
        html:not(.dark) .attendance-stats-label {
            color: #6b7280 !important;
        }
        
        /* Card values */
        .attendance-stats-value {
            font-size: 2.25rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            margin-bottom: 0.75rem !important;
            letter-spacing: -0.02em !important;
        }
        
        .dark .attendance-stats-value {
            color: #f9fafb !important;
        }
        
        html:not(.dark) .attendance-stats-value {
            color: #111827 !important;
        }
        
        /* Progress bar */
        .attendance-progress-bar {
            height: 3px !important;
            border-radius: 9999px !important;
            overflow: hidden !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
            position: relative !important;
        }
        
        html:not(.dark) .attendance-progress-bar {
            background-color: rgba(0, 0, 0, 0.08) !important;
        }
        
        .attendance-progress-fill {
            height: 3px !important;
            border-radius: 9999px !important;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
        }
        
        .attendance-progress-fill::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%) !important;
            animation: shimmer 2s infinite !important;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Color variants */
        .card-purple .attendance-stats-icon-bg {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%) !important;
        }
        
        .card-green .attendance-stats-icon-bg {
            background: linear-gradient(135deg, #6ee7b7 0%, #10b981 100%) !important;
        }
        
        .card-red .attendance-stats-icon-bg {
            background: linear-gradient(135deg, #fca5a5 0%, #ef4444 100%) !important;
        }
        
        .card-yellow .attendance-stats-icon-bg {
            background: linear-gradient(135deg, #fde047 0%, #f59e0b 100%) !important;
        }
        
        .card-blue .attendance-stats-icon-bg {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%) !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .attendance-stats-card {
                min-height: 120px !important;
                padding: 1rem !important;
            }
            .attendance-stats-value {
                font-size: 2rem !important;
            }
        }
        
        @media (min-width: 1024px) {
            .attendance-stats-card {
                min-height: 140px !important;
            }
        }
    </style>
    
    <div class="space-y-6">
        {{ $this->form }}

        <!-- Modern Statistics Cards with Light/Dark Mode Support -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            @php
                $summary = $this->getSummary();
                $total = $summary['total'] ?: 1; // Avoid division by zero
            @endphp

            <!-- Total Card -->
            <div class="attendance-stats-card card-purple">
                <div class="attendance-card-header">
                    <div class="attendance-stats-icon-bg">
                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="attendance-stats-label">Total Records</div>
                <div class="attendance-stats-value">{{ number_format($summary['total']) }}</div>
            </div>

            <!-- Present Card -->
            <div class="attendance-stats-card card-green">
                <div class="attendance-card-header">
                    <div class="attendance-stats-icon-bg">
                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="attendance-percentage-badge" style="background-color: rgba(16, 185, 129, 0.15) !important; color: #10b981 !important; border-color: rgba(16, 185, 129, 0.3) !important;">{{ $total > 0 ? round(($summary['present'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="attendance-stats-label">Present</div>
                <div class="attendance-stats-value" style="color: #10b981 !important;">{{ number_format($summary['present']) }}</div>
                <div class="attendance-progress-bar">
                    <div class="attendance-progress-fill bg-gradient-to-r from-green-400 to-emerald-500" style="width: {{ $total > 0 ? round(($summary['present'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Absent Card -->
            <div class="attendance-stats-card card-red">
                <div class="attendance-card-header">
                    <div class="attendance-stats-icon-bg">
                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="attendance-percentage-badge" style="background-color: rgba(239, 68, 68, 0.15) !important; color: #ef4444 !important; border-color: rgba(239, 68, 68, 0.3) !important;">{{ $total > 0 ? round(($summary['absent'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="attendance-stats-label">Absent</div>
                <div class="attendance-stats-value" style="color: #ef4444 !important;">{{ number_format($summary['absent']) }}</div>
                <div class="attendance-progress-bar">
                    <div class="attendance-progress-fill bg-gradient-to-r from-red-400 to-rose-500" style="width: {{ $total > 0 ? round(($summary['absent'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Late Card -->
            <div class="attendance-stats-card card-yellow">
                <div class="attendance-card-header">
                    <div class="attendance-stats-icon-bg">
                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="attendance-percentage-badge" style="background-color: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; border-color: rgba(245, 158, 11, 0.3) !important;">{{ $total > 0 ? round(($summary['late'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="attendance-stats-label">Late</div>
                <div class="attendance-stats-value" style="color: #f59e0b !important;">{{ number_format($summary['late']) }}</div>
                <div class="attendance-progress-bar">
                    <div class="attendance-progress-fill bg-gradient-to-r from-yellow-400 to-orange-500" style="width: {{ $total > 0 ? round(($summary['late'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Excused Card -->
            <div class="attendance-stats-card card-blue">
                <div class="attendance-card-header">
                    <div class="attendance-stats-icon-bg">
                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="attendance-percentage-badge" style="background-color: rgba(59, 130, 246, 0.15) !important; color: #3b82f6 !important; border-color: rgba(59, 130, 246, 0.3) !important;">{{ $total > 0 ? round(($summary['excused'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="attendance-stats-label">Excused</div>
                <div class="attendance-stats-value" style="color: #3b82f6 !important;">{{ number_format($summary['excused']) }}</div>
                <div class="attendance-progress-bar">
                    <div class="attendance-progress-fill bg-gradient-to-r from-blue-400 to-cyan-500" style="width: {{ $total > 0 ? round(($summary['excused'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Export Buttons with Better Styling -->
        <div class="flex flex-wrap gap-3 mb-6">
            <x-filament::button 
                wire:click="exportPdf" 
                color="danger" 
                icon="heroicon-o-document-arrow-down"
                size="lg"
                class="shadow-sm hover:shadow-md transition-shadow"
            >
                <span class="flex items-center gap-2">
                    Export PDF
                </span>
            </x-filament::button>
            
            <x-filament::button 
                wire:click="exportExcel" 
                color="success" 
                icon="heroicon-o-table-cells"
                size="lg"
                class="shadow-sm hover:shadow-md transition-shadow"
            >
                <span class="flex items-center gap-2">
                    Export Excel
                </span>
            </x-filament::button>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
