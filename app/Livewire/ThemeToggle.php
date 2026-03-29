<?php

namespace App\Livewire;

use App\Services\ThemeService;
use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'light';

    /**
     * Mount the component and load the current theme.
     */
    public function mount()
    {
        $this->theme = ThemeService::getUserTheme();
    }

    /**
     * Toggle between light and dark themes.
     */
    public function toggle()
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        ThemeService::setUserTheme($this->theme);
        
        // Dispatch event to update the UI
        $this->dispatch('theme-changed', theme: $this->theme);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
