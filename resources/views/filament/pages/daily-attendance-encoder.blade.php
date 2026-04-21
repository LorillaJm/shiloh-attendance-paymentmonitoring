<x-filament-panels::page>
    <form wire:submit="saveAttendance">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    Students ({{ count($students) }})
                </x-slot>

                <x-slot name="headerEnd">
                    <div class="flex gap-2">
                        <x-filament::button
                            wire:click="markAllPresent"
                            color="success"
                            size="sm"
                            type="button"
                        >
                            Mark All Present
                        </x-filament::button>

                        <x-filament::button
                            wire:click="markAllAbsent"
                            color="danger"
                            size="sm"
                            type="button"
                        >
                            Mark All Absent
                        </x-filament::button>
                    </div>
                </x-slot>

                @if(count($students) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Student No
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Session
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Remarks
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-100/60 dark:hover:bg-gray-800/60">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $student->student_no }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $student->full_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex gap-2">
                                                @php
                                                    $statusColors = [
                                                        'PRESENT' => ['bg' => '#16a34a', 'border' => '#16a34a'],
                                                        'ABSENT'  => ['bg' => '#dc2626', 'border' => '#dc2626'],
                                                        'LATE'    => ['bg' => '#d97706', 'border' => '#d97706'],
                                                        'EXCUSED' => ['bg' => '#2563eb', 'border' => '#2563eb'],
                                                    ];
                                                @endphp
                                                @foreach(config('attendance.status_options') as $value => $label)
                                                    @php $isActive = $attendanceData[$student->id]['status'] === $value; @endphp
                                                    @if($isActive)
                                                        <button
                                                            type="button"
                                                            wire:click="updateStatus({{ $student->id }}, '{{ $value }}')"
                                                            class="px-3 py-1 text-xs font-semibold rounded-md border"
                                                            style="background-color:{{ $statusColors[$value]['bg'] }};border-color:{{ $statusColors[$value]['border'] }};color:#fff;"
                                                        >
                                                            {{ $label }}
                                                        </button>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="updateStatus({{ $student->id }}, '{{ $value }}')"
                                                            class="px-3 py-1 text-xs font-semibold rounded-md border border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 bg-transparent"
                                                        >
                                                            {{ $label }}
                                                        </button>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if(isset($sessionOccurrenceData[$student->id]))
                                                @php $sod = $sessionOccurrenceData[$student->id]; @endphp
                                                <span @class([
                                                    'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full',
                                                    'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' => $sod['status'] === 'COMPLETED',
                                                    'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100' => $sod['status'] === 'SCHEDULED',
                                                    'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' => $sod['status'] === 'CANCELLED',
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' => !in_array($sod['status'], ['COMPLETED', 'SCHEDULED', 'CANCELLED']),
                                                ])>
                                                    {{ $sod['session_type'] }} &middot; {{ $sod['status'] }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500">No session</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <input
                                                type="text"
                                                wire:model.blur="attendanceData.{{ $student->id }}.remarks"
                                                placeholder="Optional remarks"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-filament::button
                            type="submit"
                            size="lg"
                        >
                            <x-filament::icon
                                icon="heroicon-o-check-circle"
                                class="w-5 h-5 mr-2"
                            />
                            Save Attendance
                        </x-filament::button>
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-filament::icon
                            icon="heroicon-o-user-group"
                            class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4"
                        />
                        <p class="text-gray-500 dark:text-gray-400">
                            No students found. Try adjusting your filters.
                        </p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </form>
</x-filament-panels::page>
