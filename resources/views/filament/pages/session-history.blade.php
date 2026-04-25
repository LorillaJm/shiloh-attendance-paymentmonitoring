<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="$refresh" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit">
                    Generate Report
                </x-filament::button>
            </div>
        </form>

        @if($this->studentId && $this->getSessions()->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Session History Report</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Session Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Teacher</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Attendance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->getSessions() as $session)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $session->session_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $session->sessionType->name }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $session->teacher?->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($session->status === 'COMPLETED') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                            @elseif($session->status === 'CANCELLED') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                            @elseif($session->status === 'NO_SHOW') bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100
                                            @endif">
                                            {{ $session->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($session->attendanceRecord)
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($session->attendanceRecord->status === 'PRESENT') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                                @elseif($session->attendanceRecord->status === 'ABSENT') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                                @elseif($session->attendanceRecord->status === 'LATE') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100
                                                @else bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                                @endif">
                                                {{ $session->attendanceRecord->status }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $session->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    Total Sessions: {{ $this->getSessions()->count() }}
                </div>
            </div>
        @elseif($this->studentId)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center text-gray-500 dark:text-gray-400">
                No sessions found for the selected criteria.
            </div>
        @endif
    </div>
</x-filament-panels::page>
