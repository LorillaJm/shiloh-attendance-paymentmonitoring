<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Payments (Last 7 Days)
        </x-slot>

        <x-slot name="headerEnd">
            <x-filament::link 
                :href="route('filament.admin.resources.payment-schedules.index')"
                tag="a"
                wire:navigate
            >
                View All
            </x-filament::link>
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800">
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Student
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Package
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Payment #
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Amount
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            Method
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($this->getRecentPayments() as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $payment->student_no }}
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    {{ $payment->student_name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full bg-info-100 px-2 py-1 text-xs font-semibold text-info-800 dark:bg-info-900 dark:text-info-200">
                                    {{ $payment->package_name }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                {{ $payment->installment_no == 0 ? 'Down Payment' : 'Payment #' . $payment->installment_no }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-success-600 dark:text-success-400">
                                ₱{{ number_format($payment->amount_due, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-center text-sm">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if($payment->payment_method === 'CASH') bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200
                                    @else bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-200
                                    @endif">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No recent payments in the last 7 days
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
