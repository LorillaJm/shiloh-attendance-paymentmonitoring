<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if($this->selectedStudentId)
            @php $student = $this->getSelectedStudent(); @endphp
            @php $summary = $this->getAttendanceSummary(); @endphp

            @if($student)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                <x-heroicon-o-user class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $student->full_name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->student_no }} &bull; Guardian: {{ $student->guardian_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Records</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $summary['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Present</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $summary['present'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Absent</div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $summary['absent'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Late</div>
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $summary['late'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Excused</div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $summary['excused'] }}</div>
                </div>
            </div>

            @if($summary['total'] > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate:</span>
                        <span class="text-lg font-bold {{ ($summary['present'] / max(1, $summary['total'])) >= 0.8 ? 'text-green-600' : (($summary['present'] / max(1, $summary['total'])) >= 0.6 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format(($summary['present'] / max(1, $summary['total'])) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full {{ ($summary['present'] / max(1, $summary['total'])) >= 0.8 ? 'bg-green-500' : (($summary['present'] / max(1, $summary['total'])) >= 0.6 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ ($summary['present'] / max(1, $summary['total'])) * 100 }}%"></div>
                    </div>
                </div>
            @endif
        @endif

        {{ $this->table }}
    </div>
</x-filament-panels::page>
