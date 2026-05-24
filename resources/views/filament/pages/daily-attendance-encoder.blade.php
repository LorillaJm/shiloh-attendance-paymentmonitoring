<x-filament-panels::page>
    <form wire:submit="saveAttendance">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    Students ({{ $totalStudents }})
                </x-slot>

                <x-slot name="headerEnd">
                    <div class="flex flex-wrap items-center gap-2">
                        <select wire:model.live="perPage" class="text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-1.5 px-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="10">10 per page</option>
                            <option value="20">20 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>

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
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Action
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
                                        <td class="px-4 py-3 whitespace-nowrap"
                                            x-data="{
                                                confirming: false,
                                                pendingStatus: '',
                                                pendingLabel: '',
                                                currentStatus: @js($attendanceData[$student->id]['status']),
                                                statusColors: {
                                                    'PRESENT': '#16a34a',
                                                    'ABSENT': '#dc2626',
                                                    'LATE': '#d97706',
                                                    'EXCUSED': '#2563eb',
                                                },
                                                requestChange(value, label) {
                                                    if (value === this.currentStatus) return;
                                                    this.pendingStatus = value;
                                                    this.pendingLabel = label;
                                                    this.confirming = true;
                                                },
                                                confirmChange() {
                                                    this.currentStatus = this.pendingStatus;
                                                    $wire.updateStatus({{ $student->id }}, this.pendingStatus);
                                                    this.confirming = false;
                                                    this.pendingStatus = '';
                                                    this.pendingLabel = '';
                                                },
                                                cancelChange() {
                                                    this.confirming = false;
                                                    this.pendingStatus = '';
                                                    this.pendingLabel = '';
                                                }
                                            }"
                                            @status-saved-{{ $student->id }}.window="currentStatus = $event.detail.status"
                                        >
                                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                                @php
                                                    $statusColors = [
                                                        'PRESENT' => ['bg' => '#16a34a', 'border' => '#16a34a'],
                                                        'ABSENT'  => ['bg' => '#dc2626', 'border' => '#dc2626'],
                                                        'LATE'    => ['bg' => '#d97706', 'border' => '#d97706'],
                                                        'EXCUSED' => ['bg' => '#2563eb', 'border' => '#2563eb'],
                                                    ];
                                                @endphp
                                                @foreach(config('attendance.status_options') as $value => $label)
                                                    <button
                                                        type="button"
                                                        x-on:click="requestChange('{{ $value }}', '{{ $label }}')"
                                                        class="px-3 py-1 text-xs font-semibold rounded-md border transition-all duration-150"
                                                        x-bind:style="currentStatus === '{{ $value }}'
                                                            ? 'background-color:{{ $statusColors[$value]['bg'] }};border-color:{{ $statusColors[$value]['border'] }};color:#fff;'
                                                            : ''"
                                                        x-bind:class="currentStatus === '{{ $value }}'
                                                            ? ''
                                                            : 'border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 bg-transparent'"
                                                    >
                                                        {{ $label }}
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Confirmation popup --}}
                                            <div x-show="confirming" x-cloak x-transition.opacity.duration.150ms
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                                x-on:keydown.escape.window="cancelChange()"
                                            >
                                                <div x-on:click.away="cancelChange()" x-transition.scale.origin.center
                                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-5 w-80 max-w-[90vw] text-center border border-gray-200 dark:border-gray-700"
                                                >
                                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full"
                                                        x-bind:style="'background-color:' + (statusColors[pendingStatus] || '#6b7280') + '20'"
                                                    >
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                            x-bind:style="'color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                                        Change Status
                                                    </h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                        Mark <span class="font-semibold text-gray-900 dark:text-white">{{ $student->full_name }}</span>
                                                        as <span class="font-bold" x-text="pendingLabel"
                                                            x-bind:style="'color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        ></span>?
                                                    </p>
                                                    <div class="flex gap-2 justify-center">
                                                        <button type="button" x-on:click="cancelChange()"
                                                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                            Cancel
                                                        </button>
                                                        <button type="button" x-on:click="confirmChange()"
                                                            class="px-4 py-2 text-sm font-semibold rounded-lg text-white transition-colors"
                                                            x-bind:style="'background-color:' + (statusColors[pendingStatus] || '#6b7280')"
                                                        >
                                                            Yes, confirm
                                                        </button>
                                                    </div>
                                                </div>
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
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <button
                                                type="button"
                                                wire:click="saveIndividualAttendance({{ $student->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="saveIndividualAttendance({{ $student->id }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 disabled:opacity-50 transition-colors"
                                            >
                                                <svg wire:loading.remove wire:target="saveIndividualAttendance({{ $student->id }})" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <svg wire:loading wire:target="saveIndividualAttendance({{ $student->id }})" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                                </svg>
                                                Save
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($this->totalPages() > 1)
                        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 px-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Showing {{ (($currentPage - 1) * $perPage) + 1 }} to {{ min($currentPage * $perPage, $totalStudents) }} of {{ $totalStudents }} students
                            </p>
                            <div class="flex items-center gap-1">
                                <button type="button" wire:click="previousPage" @disabled($currentPage <= 1)
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    Previous
                                </button>
                                @for($i = 1; $i <= $this->totalPages(); $i++)
                                    @if($this->totalPages() <= 7 || $i <= 2 || $i >= $this->totalPages() - 1 || abs($i - $currentPage) <= 1)
                                        <button type="button" wire:click="goToPage({{ $i }})"
                                            class="px-3 py-1.5 text-sm font-medium rounded-lg border transition-colors {{ $i === $currentPage ? 'bg-primary-600 text-white border-primary-600' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            {{ $i }}
                                        </button>
                                    @elseif($i === 3 || $i === $this->totalPages() - 2)
                                        <span class="px-2 text-gray-400">...</span>
                                    @endif
                                @endfor
                                <button type="button" wire:click="nextPage" @disabled($currentPage >= $this->totalPages())
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    Next
                                </button>
                            </div>
                        </div>
                    @endif

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
