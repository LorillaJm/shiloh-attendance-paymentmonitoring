<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Payments (Last 7 Days)
        </x-slot>

        <x-slot name="headerEnd">
            @php
                try {
                    $viewAllUrl = route('filament.admin.resources.payment-schedules.index');
                } catch (\Exception $e) {
                    $viewAllUrl = null;
                }
            @endphp
            
            @if($viewAllUrl)
                <x-filament::link 
                    :href="$viewAllUrl"
                    tag="a"
                    wire:navigate
                >
                    View All
                </x-filament::link>
            @endif
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px]" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr class="bg-gray-50 dark:bg-[#0f1520]">
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Student
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Package
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Payment #
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Amount
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-[#5b6a82]">
                            Method
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getRecentPayments() as $payment)
                        <tr class="border-b border-gray-100 dark:border-white/[0.04] transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-[#1a2332]">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-[#c8d3e0]">
                                {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900 dark:text-[#f0f4f8]">
                                    {{ $payment->student_no }}
                                </div>
                                <div class="text-gray-500 dark:text-[#8b9ab5]" style="font-size: 0.8125rem;">
                                    {{ $payment->student_name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full bg-info-100 px-2 py-1 text-xs font-semibold text-info-800 dark:bg-info-900 dark:text-info-200">
                                    {{ $payment->package_name }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-[#c8d3e0]">
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
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-[#5b6a82]">
                                No recent payments in the last 7 days
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
