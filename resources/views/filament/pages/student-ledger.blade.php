<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @if($student)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Student Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Student Number:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $student->student_no }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Guardian:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $student->guardian_name }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Contact:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">{{ $student->guardian_contact }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <x-filament::button wire:click="exportPdf" color="danger" icon="heroicon-o-document-arrow-down">
                    Export PDF
                </x-filament::button>
            </div>

            @foreach($enrollments as $enrollment)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $enrollment->package->name }}</h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Enrolled: {{ $enrollment->enrollment_date->format('F d, Y') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Fee</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($enrollment->total_fee, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total Paid</div>
                            <div class="text-xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($enrollment->total_paid, 2) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Balance</div>
                            <div class="text-xl font-bold text-red-600 dark:text-red-400">₱{{ number_format($enrollment->remaining_balance_computed, 2) }}</div>
                        </div>
                    </div>

                    <div class="overflow-x-auto -mx-2 px-2">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-2 text-gray-900 dark:text-gray-100">Installment</th>
                                <th class="text-left py-2 text-gray-900 dark:text-gray-100">Due Date</th>
                                <th class="text-right py-2 text-gray-900 dark:text-gray-100">Amount</th>
                                <th class="text-center py-2 text-gray-900 dark:text-gray-100">Status</th>
                                <th class="text-left py-2 text-gray-900 dark:text-gray-100">Paid Date</th>
                                <th class="text-left py-2 text-gray-900 dark:text-gray-100">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollment->paymentSchedules as $schedule)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-2 text-gray-900 dark:text-gray-100">
                                        {{ $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}" }}
                                    </td>
                                    <td class="py-2 text-gray-900 dark:text-gray-100">{{ $schedule->due_date ? $schedule->due_date->format('Y-m-d') : '-' }}</td>
                                    <td class="text-right py-2 text-gray-900 dark:text-gray-100">₱{{ number_format($schedule->amount_due, 2) }}</td>
                                    <td class="text-center py-2">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $schedule->status === 'PAID' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                            {{ $schedule->status }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-gray-900 dark:text-gray-100">{{ $schedule->paid_at ? $schedule->paid_at->format('Y-m-d') : '-' }}</td>
                                    <td class="py-2 text-gray-900 dark:text-gray-100">{{ $schedule->payment_method ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                Please select a student to view their ledger
            </div>
        @endif
    </div>
</x-filament-panels::page>
