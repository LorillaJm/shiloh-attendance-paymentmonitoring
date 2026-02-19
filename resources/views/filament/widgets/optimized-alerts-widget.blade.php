<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Alerts & Quick Actions
        </x-slot>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach($this->getAlerts() as $alert)
                <a href="{{ $alert['url'] }}" 
                   class="flex items-center gap-4 rounded-lg border p-4 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                   wire:navigate>
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full 
                            @if($alert['color'] === 'danger') bg-danger-100 dark:bg-danger-900
                            @elseif($alert['color'] === 'warning') bg-warning-100 dark:bg-warning-900
                            @else bg-info-100 dark:bg-info-900
                            @endif">
                            <x-filament::icon 
                                :icon="$alert['icon']" 
                                class="h-6 w-6 
                                    @if($alert['color'] === 'danger') text-danger-600 dark:text-danger-400
                                    @elseif($alert['color'] === 'warning') text-warning-600 dark:text-warning-400
                                    @else text-info-600 dark:text-info-400
                                    @endif"
                            />
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ $alert['title'] }}
                        </p>
                        <p class="text-2xl font-bold 
                            @if($alert['color'] === 'danger') text-danger-600 dark:text-danger-400
                            @elseif($alert['color'] === 'warning') text-warning-600 dark:text-warning-400
                            @else text-info-600 dark:text-info-400
                            @endif">
                            {{ number_format($alert['count']) }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <x-filament::icon 
                            icon="heroicon-o-chevron-right" 
                            class="h-5 w-5 text-gray-400"
                        />
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
