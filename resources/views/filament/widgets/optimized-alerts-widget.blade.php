<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Links
        </x-slot>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach($this->getAlerts() as $alert)
                <a 
                    href="{{ $alert['url'] }}" 
                    wire:navigate
                    class="flex flex-col items-center gap-2 rounded-xl p-3 text-center transition-all duration-200 border border-gray-200 dark:border-white/[0.04] hover:bg-gray-50 dark:hover:bg-[#1a2332] hover:border-gray-300 dark:hover:border-white/[0.08]"
                >
                    <x-filament::icon
                        :icon="$alert['icon']"
                        class="h-6 w-6 {{ $alert['color'] }}"
                    />
                    <span class="text-xs font-medium text-gray-500 dark:text-[#8b9ab5]">
                        {{ $alert['label'] }}
                    </span>
                    <span class="text-lg font-bold {{ $alert['color'] }}">
                        {{ $alert['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
