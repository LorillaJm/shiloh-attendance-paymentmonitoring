<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Collections</div>
                <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                    ₱{{ number_format($this->getSummary()['total_amount'], 2) }}
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Transactions</div>
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                    {{ $this->getSummary()['total_count'] }}
                </div>
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
    </div>
</x-filament-panels::page>
