<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">
                Select Month & Year
            </x-slot>

            <div class="flex items-center gap-4">
                <select
                    wire:model.live="selectedMonth"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m)->format('F') }}</option>
                    @endfor
                </select>

                <select
                    wire:model.live="selectedYear"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $this->getMonthName() }} {{ $selectedYear }}
                </span>
            </div>
        </x-filament::section>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
