<x-filament-panels::page>
    @if(auth()->user()->isAdmin())
        {{-- Admin Command Center Dashboard --}}
        <div class="space-y-6">
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="[
                    'default' => 1,
                    'sm' => 2,
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 6,
                    '2xl' => 6,
                ]"
            />
        </div>
    @else
        {{-- User Dashboard - Mobile-First Attendance Encoding --}}
        <div class="space-y-6">
            {{-- Welcome Banner --}}
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-3xl p-6 shadow-soft-lg border border-primary-100 dark:border-primary-800/30">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white dark:bg-gray-800 rounded-2xl shadow-soft">
                        <x-heroicon-o-user-circle class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Welcome, {{ auth()->user()->name }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Attendance Encoder</p>
                    </div>
                </div>
            </div>

            {{-- Widgets --}}
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="[
                    'default' => 1,
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 4,
                    'xl' => 4,
                ]"
            />
        </div>
    @endif
</x-filament-panels::page>
