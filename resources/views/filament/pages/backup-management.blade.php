<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Action Bar --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">Database Backup</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Export all tables to Excel files in the <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $this->backupConfig['path'] }}</code> folder.
                    </p>
                </div>
                <x-filament::button
                    wire:click="runBackupNow"
                    wire:loading.attr="disabled"
                    icon="heroicon-o-arrow-down-tray"
                    size="lg"
                >
                    <span wire:loading.remove wire:target="runBackupNow">Run Backup Now</span>
                    <span wire:loading wire:target="runBackupNow">Running...</span>
                </x-filament::button>
            </div>
        </div>

        {{-- Configuration Overview --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Schedule</p>
                <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $this->backupConfig['schedule'] }}</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tables</p>
                <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $this->backupConfig['tables_count'] }} configured</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Retention</p>
                <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ $this->backupConfig['retention_months'] }} months</p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Backups</p>
                <p class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">{{ count($this->backups) }}</p>
            </div>
        </div>

        {{-- Backup History --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="p-5 border-b border-gray-200 dark:border-white/10">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Backup History</h3>
            </div>

            @if(count($this->backups) === 0)
                <div class="p-8 text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3">
                        <x-heroicon-o-circle-stack class="w-6 h-6 text-gray-400" />
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No backups yet. Click "Run Backup Now" to create your first backup.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Period</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tables</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rows</th>
                                <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date/Time</th>
                                <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($this->backups as $backup)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-5 py-3 font-medium text-gray-950 dark:text-white">
                                        {{ $backup['folder'] }}
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $statusColors = [
                                                'success' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                                'partial' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'failed'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                                'running' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                                'unknown' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                            ];
                                            $color = $statusColors[$backup['status']] ?? $statusColors['unknown'];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                            {{ ucfirst($backup['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        {{ $backup['total_tables'] }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-300">
                                        {{ number_format($backup['total_rows'] ?? 0) }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                        {{ $backup['started_at'] ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <x-filament::button
                                            wire:click="deleteBackup('{{ $backup['folder'] }}')"
                                            wire:confirm="Are you sure you want to delete the {{ $backup['folder'] }} backup?"
                                            color="danger"
                                            size="xs"
                                            icon="heroicon-o-trash"
                                            outlined
                                        >
                                            Delete
                                        </x-filament::button>
                                    </td>
                                </tr>

                                {{-- Expandable error details --}}
                                @if(!empty($backup['errors']))
                                    <tr>
                                        <td colspan="6" class="px-5 py-2 bg-red-50 dark:bg-red-900/10">
                                            <p class="text-xs font-medium text-red-700 dark:text-red-400">Errors:</p>
                                            <ul class="mt-1 text-xs text-red-600 dark:text-red-400 list-disc list-inside">
                                                @foreach($backup['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Info Card --}}
        <div class="fi-section rounded-xl bg-blue-50 dark:bg-blue-900/10 ring-1 ring-blue-200 dark:ring-blue-800/30 p-5">
            <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">How it works</h4>
            <ul class="space-y-1.5 text-xs text-blue-700 dark:text-blue-400">
                <li>• Backups run automatically on the <strong>1st of every month</strong> at 2:00 AM (Manila time).</li>
                <li>• Each table is exported as a separate <strong>.xlsx</strong> file with bold headers and auto-sized columns.</li>
                <li>• Files are saved to <code class="bg-blue-100 dark:bg-blue-900/30 px-1 rounded">{{ $this->backupConfig['path'] }}/YYYY-MM/</code> in the project root.</li>
                <li>• A <strong>backup-summary.json</strong> and <strong>backup-log.txt</strong> are generated with each run.</li>
                <li>• Backups older than <strong>{{ $this->backupConfig['retention_months'] }} months</strong> are automatically cleaned up.</li>
                <li>• Manual CLI: <code class="bg-blue-100 dark:bg-blue-900/30 px-1 rounded">php artisan backup:run</code></li>
            </ul>
        </div>

    </div>
</x-filament-panels::page>
