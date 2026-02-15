<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Database Backup</h3>
            
            <div class="mb-6">
                <x-filament::button wire:click="generateBackup" color="primary">
                    Generate Backup Instructions
                </x-filament::button>
            </div>

            <div class="space-y-6">
                @foreach($this->getBackupInstructions() as $method)
                    <div class="border dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-semibold text-md mb-3">{{ $method['title'] }}</h4>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            @foreach($method['steps'] as $step)
                                <li>{{ $step }}</li>
                            @endforeach
                        </ol>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Important Notes</h4>
            <ul class="list-disc list-inside space-y-1 text-sm text-yellow-700 dark:text-yellow-300">
                <li>Always test your backups by restoring them to a test environment</li>
                <li>Store backups in multiple locations (local + cloud)</li>
                <li>Encrypt sensitive backup files</li>
                <li>Document your backup and restore procedures</li>
                <li>Set up automated backup monitoring and alerts</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
