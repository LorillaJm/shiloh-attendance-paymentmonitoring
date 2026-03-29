<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="text-center mt-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Login with your credentials. You will be automatically redirected to your dashboard.
        </p>
    </div>
</x-filament-panels::page.simple>
