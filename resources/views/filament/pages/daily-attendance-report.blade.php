<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">
                Select Date
            </x-slot>

            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <input
                    type="date"
                    wire:model.live="selectedDate"
                    max="{{ now()->format('Y-m-d') }}"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($selectedDate)->format('l, F d, Y') }}
                </span>
            </div>
        </x-filament::section>
    </div>

    @php
        $summary = $this->getSummary();
    @endphp

    @php
        $totalCount = $summary['total'] ?: 1;
        $cards = [
            [
                'label' => 'Total Records',
                'value' => $summary['total'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
                'iconBg' => 'linear-gradient(135deg, #6366f1, #8b5cf6)',
                'color' => '#6366f1',
                'percent' => 100,
                'showBar' => false,
            ],
            [
                'label' => 'Present',
                'value' => $summary['present'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'iconBg' => 'linear-gradient(135deg, #10b981, #059669)',
                'color' => '#10b981',
                'percent' => round(($summary['present'] / $totalCount) * 100),
                'showBar' => true,
            ],
            [
                'label' => 'Absent',
                'value' => $summary['absent'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'iconBg' => 'linear-gradient(135deg, #ef4444, #dc2626)',
                'color' => '#ef4444',
                'percent' => round(($summary['absent'] / $totalCount) * 100),
                'showBar' => true,
            ],
            [
                'label' => 'Late',
                'value' => $summary['late'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'iconBg' => 'linear-gradient(135deg, #f59e0b, #d97706)',
                'color' => '#f59e0b',
                'percent' => round(($summary['late'] / $totalCount) * 100),
                'showBar' => true,
            ],
            [
                'label' => 'Excused',
                'value' => $summary['excused'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                'iconBg' => 'linear-gradient(135deg, #3b82f6, #2563eb)',
                'color' => '#3b82f6',
                'percent' => round(($summary['excused'] / $totalCount) * 100),
                'showBar' => true,
            ],
        ];
    @endphp

    <div class="attendance-stats-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        @foreach($cards as $card)
            <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 1.25rem; transition: all 0.2s; position: relative; overflow: hidden;">
                {{-- Subtle accent border top --}}
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $card['color'] }};"></div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: {{ $card['iconBg'] }}; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px {{ $card['color'] }}33;">
                        <svg style="width: 22px; height: 22px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon'] !!}</svg>
                    </div>
                    @if($summary['total'] > 0 && $card['showBar'])
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ $card['color'] }}; background: {{ $card['color'] }}15; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                            {{ $card['percent'] }}%
                        </span>
                    @endif
                </div>

                <p style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 0.35rem;">{{ $card['label'] }}</p>
                <p style="font-size: 2rem; font-weight: 800; color: {{ $card['color'] }}; line-height: 1.1;">{{ $card['value'] }}</p>

                @if($card['showBar'])
                    <div style="margin-top: 0.85rem; width: 100%; height: 5px; background: rgba(255,255,255,0.06); border-radius: 9999px; overflow: hidden;">
                        <div style="width: {{ $card['percent'] }}%; height: 100%; background: {{ $card['color'] }}; border-radius: 9999px; transition: width 0.5s ease;"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <style>
        @media (max-width: 1024px) {
            .attendance-stats-grid { grid-template-columns: repeat(3, 1fr) !important; }
        }
        @media (max-width: 640px) {
            .attendance-stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 400px) {
            .attendance-stats-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    <div class="flex flex-wrap gap-2 mb-4">
        <x-filament::button wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
            <span wire:loading.remove wire:target="exportPdf">Export PDF</span>
            <span wire:loading wire:target="exportPdf">Generating PDF...</span>
        </x-filament::button>
        <x-filament::button wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel" color="success" icon="heroicon-o-table-cells">
            <span wire:loading.remove wire:target="exportExcel">Export Excel</span>
            <span wire:loading wire:target="exportExcel">Generating Excel...</span>
        </x-filament::button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
