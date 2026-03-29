<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-calculator class="w-5 h-5" />
                <span>Salary Calculation</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Calculate monthly salary based on attendance records
        </x-slot>

        <div class="space-y-6">
            {{-- Month/Year Selector --}}
            <div class="grid grid-cols-2 gap-4">
                {{ $this->form }}
            </div>

            {{-- Salary Breakdown --}}
            @if($salaryData)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            Salary Breakdown
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getSalaryBreakdown() as $label => $value)
                            <div class="px-4 py-3 flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 
                                    @if($label === 'Total Salary') text-lg font-bold text-primary-600 dark:text-primary-400 @endif">
                                    {{ $value }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Attendance Records (for daily salary) --}}
                @if($salaryData['type'] === 'daily' && count($this->getAttendanceRecords()) > 0)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                Attendance Records
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Hours
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($this->getAttendanceRecords() as $record)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                                {{ $record['date'] }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($record['status'] === 'Present') bg-success-100 text-success-800 dark:bg-success-800 dark:text-success-100
                                                    @elseif($record['status'] === 'Late') bg-warning-100 text-warning-800 dark:bg-warning-800 dark:text-warning-100
                                                    @else bg-danger-100 text-danger-800 dark:bg-danger-800 dark:text-danger-100 @endif">
                                                    {{ $record['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-gray-100">
                                                {{ $record['hours'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif($salaryData['type'] === 'daily' && count($this->getAttendanceRecords()) === 0)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-6 text-center">
                        <x-heroicon-o-exclamation-triangle class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" />
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            No attendance records found for this period.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            The calculated salary is ₱0.00
                        </p>
                    </div>
                @endif
            @else
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-6 text-center">
                    <x-heroicon-o-calculator class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" />
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Select a month and year to calculate salary
                    </p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
