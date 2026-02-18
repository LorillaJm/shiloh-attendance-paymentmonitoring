<x-filament-panels::page>
    <div class="space-y-6">
        @forelse($this->getStudents() as $student)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-4">{{ $student->full_name }}</h2>
                <p class="text-gray-600 mb-4">Student No: {{ $student->student_no }}</p>

                {{-- Enrollment & Payment Status --}}
                @if($student->enrollments->isNotEmpty())
                    @foreach($student->enrollments as $enrollment)
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold mb-2">Package: {{ $enrollment->package->name }}</h3>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Package Period</p>
                                    <p class="font-medium">
                                        {{ $enrollment->package_start_date?->format('M d, Y') }} - 
                                        {{ $enrollment->package_end_date?->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Fee</p>
                                    <p class="font-medium">₱{{ number_format($enrollment->total_fee, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total Paid</p>
                                    <p class="font-medium text-green-600">₱{{ number_format($enrollment->total_paid, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Balance</p>
                                    <p class="font-medium text-red-600">₱{{ number_format($enrollment->remaining_balance_computed, 2) }}</p>
                                </div>
                            </div>

                            {{-- Payment Schedule --}}
                            <h4 class="font-semibold mb-2">Payment Schedule</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paid Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($enrollment->paymentSchedules as $schedule)
                                            <tr>
                                                <td class="px-4 py-2">{{ $schedule->due_date?->format('M d, Y') }}</td>
                                                <td class="px-4 py-2">₱{{ number_format($schedule->amount_due, 2) }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        @if($schedule->status === 'PAID') bg-green-100 text-green-800
                                                        @elseif($schedule->computed_status === 'OVERDUE') bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800
                                                        @endif">
                                                        {{ $schedule->computed_status }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2">{{ $schedule->paid_at?->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Recent Attendance --}}
                <div class="mb-6 border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Recent Attendance</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($student->attendanceRecords()->latest('attendance_date')->limit(10)->get() as $attendance)
                                    <tr>
                                        <td class="px-4 py-2">{{ $attendance->attendance_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($attendance->status === 'PRESENT') bg-green-100 text-green-800
                                                @elseif($attendance->status === 'ABSENT') bg-red-100 text-red-800
                                                @elseif($attendance->status === 'LATE') bg-yellow-100 text-yellow-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">{{ $attendance->remarks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Session History --}}
                <div class="border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Session History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session Type</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Attendance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($student->sessionOccurrences()->latest('session_date')->limit(20)->get() as $session)
                                    <tr>
                                        <td class="px-4 py-2">{{ $session->session_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-2">{{ $session->sessionType->name }}</td>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($session->status === 'COMPLETED') bg-green-100 text-green-800
                                                @elseif($session->status === 'CANCELLED') bg-red-100 text-red-800
                                                @elseif($session->status === 'NO_SHOW') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ $session->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($session->attendanceRecord)
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($session->attendanceRecord->status === 'PRESENT') bg-green-100 text-green-800
                                                    @elseif($session->attendanceRecord->status === 'ABSENT') bg-red-100 text-red-800
                                                    @elseif($session->attendanceRecord->status === 'LATE') bg-yellow-100 text-yellow-800
                                                    @else bg-blue-100 text-blue-800
                                                    @endif">
                                                    {{ $session->attendanceRecord->status }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                No students found for your account.
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
