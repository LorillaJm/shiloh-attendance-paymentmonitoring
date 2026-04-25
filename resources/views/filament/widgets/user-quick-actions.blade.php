<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            {{-- Today's Date Display --}}
            <div class="text-center py-4">
                <div class="inline-flex items-center gap-2 mb-2 text-gray-500 dark:text-[#8b9ab5]">
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
                    class="inline-flex items-center gap-3 px-8 py-4 text-white font-semibold rounded-2xl transition-all duration-200 transform hover:scale-105"
                    style="background: linear-gradient(135deg, #14b8a6, #0d9488); box-shadow: 0 4px 20px rgba(94, 234, 212, 0.2);"
                >
                    <x-heroicon-o-pencil-square class="w-6 h-6" />
                    <span class="text-lg">Encode Attendance</span>
                </a>
            </div>

            {{-- Quick Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 pt-4">
                <div class="text-center p-4 rounded-xl bg-gray-100 dark:bg-[#1a2332] border border-gray-200 dark:border-white/[0.06]">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ \App\Models\AttendanceRecord::whereDate('attendance_date', now('Asia/Manila')->format('Y-m-d'))->count() }}
                    </div>
                    <div class="text-xs mt-1 text-gray-500 dark:text-[#8b9ab5]">
                        Total Today
                    </div>
                </div>

                <div class="text-center p-4 rounded-xl bg-gray-100 dark:bg-[#1a2332] border border-gray-200 dark:border-white/[0.06]">
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ \App\Models\AttendanceRecord::whereDate('attendance_date', now('Asia/Manila')->format('Y-m-d'))->where('status', 'PRESENT')->count() }}
                    </div>
                    <div class="text-xs mt-1 text-gray-500 dark:text-[#8b9ab5]">
                        Present
                    </div>
                </div>
            </div>

            {{-- Helper Text --}}
            <div class="text-center text-sm pt-2 text-gray-400 dark:text-[#5b6a82]">
                <p>Click the button above to mark today's attendance</p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
