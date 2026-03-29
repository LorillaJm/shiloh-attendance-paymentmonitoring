<x-filament-panels::page>
    @php
        $data = $this->getDashboardData();
    @endphp

    @if(!$data || $data['students']->isEmpty())
        <div class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No Students Found</h3>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">No students are currently linked to your account.</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">Please contact the administrator to link your children to your account.</p>
        </div>
    @else
        {{-- Alert Summary --}}
        @if($data['alerts']['total'] > 0)
            <div class="mb-4 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 dark:bg-red-900/20">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-300">
                        You have {{ $data['alerts']['total'] }} notification(s) requiring attention.
                        <a href="{{ route('filament.parent.pages.notifications') }}" class="underline">View all</a>
                    </p>
                </div>
            </div>
        @endif

        {{-- Students Overview --}}
        @foreach($data['students'] as $student)
            <div class="mb-6 space-y-4">
                {{-- Student Header --}}
                <div class="overflow-hidden rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 p-6 text-white shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-2xl font-bold backdrop-blur-sm">
                            {{ substr($student->first_name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold">{{ $student->full_name }}</h2>
                            <p class="mt-1 text-sm opacity-90">{{ $student->student_no }}</p>
                            <p class="mt-1 text-sm opacity-80">{{ $student->package_name }}</p>
                        </div>
                        <div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $student->status === 'ACTIVE' ? 'bg-green-500' : 'bg-gray-500' }}">
                                {{ $student->status }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Today's Attendance --}}
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Today's Status</p>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $student->today_attendance?->status ?? 'Not Marked' }}
                                </p>
                                @if($student->today_attendance?->remarks)
                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ Str::limit($student->today_attendance->remarks, 30) }}</p>
                                @endif
                            </div>
                            <div class="rounded-lg bg-blue-50 p-2 dark:bg-blue-900/20">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Balance Due</p>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($student->remaining_balance, 2) }}</p>
                            </div>
                            <div class="rounded-lg bg-red-50 p-2 dark:bg-red-900/20">
                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Sessions --}}
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Sessions Left</p>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">{{ $student->remaining_sessions }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">of {{ $student->total_sessions }} total</p>
                            </div>
                            <div class="rounded-lg bg-green-50 p-2 dark:bg-green-900/20">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- This Month --}}
                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">This Month</p>
                                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-white">{{ $data['summary']['attendance']['present'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Present Days</p>
                            </div>
                            <div class="rounded-lg bg-purple-50 p-2 dark:bg-purple-900/20">
                                <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$loop->last)
                <hr class="my-6 border-gray-200 dark:border-gray-700">
            @endif
        @endforeach

        {{-- Recent Activity --}}
        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            {{-- Recent Attendance --}}
            <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Recent Attendance</h3>
                <div class="space-y-2">
                    @forelse($data['recent_attendance'] as $record)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $record->student->full_name }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $record->attendance_date->format('M d, Y') }}
                                </p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $record->status === 'PRESENT' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $record->status === 'ABSENT' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                {{ $record->status === 'LATE' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                {{ $record->status === 'EXCUSED' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}">
                                {{ $record->status }}
                            </span>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No records yet</p>
                    @endforelse
                </div>
                <div class="mt-3">
                    <a href="{{ route('filament.parent.pages.my-children-attendance') }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View all attendance →
                    </a>
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Recent Payments</h3>
                <div class="space-y-2">
                    @forelse($data['recent_payments'] as $payment)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $payment->enrollment->student->full_name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-500">
                                    {{ $payment->transaction_date->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No payments yet</p>
                    @endforelse
                </div>
                <div class="mt-3">
                    <a href="{{ route('filament.parent.pages.my-children-payments') }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View all payments →
                    </a>
                </div>
            </div>

            {{-- Upcoming Sessions --}}
            <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Upcoming Sessions</h3>
                <div class="space-y-2">
                    @forelse($data['upcoming_sessions'] as $session)
                        <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $session->sessionType->name }}
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $session->student->full_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $session->session_date->format('M d, Y') }} • 
                                {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                            </p>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">No upcoming sessions</p>
                    @endforelse
                </div>
                <div class="mt-3">
                    <a href="{{ route('filament.parent.pages.my-children-sessions') }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View all sessions →
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
