<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">
                Select Date
            </x-slot>

            <div class="flex items-center gap-4">
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

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $summary['total'] }}</div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-green-600 dark:text-green-400">Present</div>
            <div class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $summary['present'] }}</div>
        </div>

        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-red-600 dark:text-red-400">Absent</div>
            <div class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $summary['absent'] }}</div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Late</div>
            <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 mt-1">{{ $summary['late'] }}</div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg shadow p-4">
            <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Excused</div>
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $summary['excused'] }}</div>
        </div>
    </div>

    <div class="flex gap-2 mb-4">
        <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
            Export PDF
        </x-filament::button>
        <x-filament::button wire:click="exportExcel" color="success" icon="heroicon-o-table-cells">
            Export Excel
        </x-filament::button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
