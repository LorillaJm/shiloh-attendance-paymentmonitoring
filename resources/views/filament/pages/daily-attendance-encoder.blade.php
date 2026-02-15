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
                                        Remarks
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($students as $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $student->student_no }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $student->full_name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex gap-2">
                                                @foreach(config('attendance.status_options') as $value => $label)
                                                    <button
                                                        type="button"
                                                        wire:click="updateStatus({{ $student->id }}, '{{ $value }}')"
                                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors
                                                            @if($attendanceData[$student->id]['status'] === $value)
                                                                @switch($value)
                                                                    @case('PRESENT')
                                                                        bg-green-600 text-white
                                                                        @break
                                                                    @case('ABSENT')
                                                                        bg-red-600 text-white
                                                                        @break
                                                                    @case('LATE')
                                                                        bg-yellow-600 text-white
                                                                        @break
                                                                    @case('EXCUSED')
                                                                        bg-blue-600 text-white
                                                                        @break
                                                                @endswitch
                                                            @else
                                                                bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600
                                                            @endif
                                                        "
                                                    >
                                                        {{ $label }}
                                                    </button>
                                                @endforeach
                                            </div>
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
