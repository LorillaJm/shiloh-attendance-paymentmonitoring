<?php

namespace App\Services;

class ThemeService
{
    /**
     * Get the current user's theme preference.
     * 
     * @return string The theme ('light' or 'dark')
     */
    public static function getUserTheme(): string
    {
        $user = auth()->user();
        return $user?->theme ?? 'light';
    }

    /**
     * Set the current user's theme preference.
     * 
     * @param string $theme The theme to set ('light' or 'dark')
     * @return void
     */
    public static function setUserTheme(string $theme): void
    {
        $user = auth()->user();
        
        // Validate theme value
        if ($user && in_array($theme, ['light', 'dark'])) {
            $user->update(['theme' => $theme]);
        }
    }
}
