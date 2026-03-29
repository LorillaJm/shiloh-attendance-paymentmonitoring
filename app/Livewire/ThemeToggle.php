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
        
        // Update theme immediately with JavaScript
        $this->js("
            const theme = '{$this->theme}';
            document.documentElement.dataset.theme = theme;
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            localStorage.setItem('shiloh-theme', theme);
            
            // Trigger a custom event for any listeners
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
        ");
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
