<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Today's Date Display --}}
            <div class="text-center py-4">
                <div class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 mb-2">
                    <x-heroicon-o-calendar class="w-5 h-5" />
                    <span class="text-sm font-medium">Today</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $this->getTodayDate() }}
                </h2>
            </div>

            {{-- Quick Action Button --}}
            <div class="flex justify-center">
                <a 
                    href="{{ route('filament.admin.pages.daily-attendance-encoder') }}"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                >
                    <x-heroicon-o-pencil-square class="w-6 h-6" />
                    <span class="text-lg">Encode Attendance</span>
                </a>
            </div>

            {{-- Quick Stats Grid --}}
            <div class="grid grid-cols-2 gap-4 pt-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ \App\Models\AttendanceRecord::whereDate('attendance_date', now('Asia/Manila')->format('Y-m-d'))->count() }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Total Today
                    </div>
                </div>

                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ \App\Models\AttendanceRecord::whereDate('attendance_date', now('Asia/Manila')->format('Y-m-d'))->where('status', 'PRESENT')->count() }}
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Present
                    </div>
                </div>
            </div>

            {{-- Helper Text --}}
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 pt-2">
                <p>Click the button above to mark today's attendance</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
