<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <!-- Sleek Dark Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            @php
                $summary = $this->getSummary();
                $total = $summary['total'] ?: 1; // Avoid division by zero
            @endphp

            <!-- Total Card -->
            <div class="relative bg-gray-900 dark:bg-gray-950 rounded-xl border border-gray-700 dark:border-gray-800 p-5 overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Total Records</div>
                <div class="text-3xl font-bold text-white">{{ $summary['total'] }}</div>
            </div>

            <!-- Present Card -->
            <div class="relative bg-gray-900 dark:bg-gray-950 rounded-xl border border-gray-700 dark:border-gray-800 p-5 overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-green-400">{{ $total > 0 ? round(($summary['present'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Present</div>
                <div class="text-3xl font-bold text-green-400 mb-3">{{ $summary['present'] }}</div>
                <div class="w-full bg-gray-800 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $total > 0 ? round(($summary['present'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Absent Card -->
            <div class="relative bg-gray-900 dark:bg-gray-950 rounded-xl border border-gray-700 dark:border-gray-800 p-5 overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-rose-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-red-400">{{ $total > 0 ? round(($summary['absent'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Absent</div>
                <div class="text-3xl font-bold text-red-400 mb-3">{{ $summary['absent'] }}</div>
                <div class="w-full bg-gray-800 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-red-500 to-rose-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $total > 0 ? round(($summary['absent'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Late Card -->
            <div class="relative bg-gray-900 dark:bg-gray-950 rounded-xl border border-gray-700 dark:border-gray-800 p-5 overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-500 to-orange-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-yellow-400">{{ $total > 0 ? round(($summary['late'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Late</div>
                <div class="text-3xl font-bold text-yellow-400 mb-3">{{ $summary['late'] }}</div>
                <div class="w-full bg-gray-800 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $total > 0 ? round(($summary['late'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>

            <!-- Excused Card -->
            <div class="relative bg-gray-900 dark:bg-gray-950 rounded-xl border border-gray-700 dark:border-gray-800 p-5 overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-600"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-blue-400">{{ $total > 0 ? round(($summary['excused'] / $total) * 100) : 0 }}%</span>
                </div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Excused</div>
                <div class="text-3xl font-bold text-blue-400 mb-3">{{ $summary['excused'] }}</div>
                <div class="w-full bg-gray-800 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $total > 0 ? round(($summary['excused'] / $total) * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
                Export PDF
            </x-filament::button>
            <x-filament::button wire:click="exportExcel" color="success" icon="heroicon-o-table-cells">
                Export Excel
            </x-filament::button>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
