/**
 * Apply theme to the document
 * @param {string} theme - The theme to apply ('light' or 'dark')
 */
function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    document.documentElement.classList.toggle('dark', theme === 'dark');
    localStorage.setItem('theme', theme);
}

/**
 * Initialize theme on page load
 */
document.addEventListener('DOMContentLoaded', () => {
    // Get theme from data attribute set by server or fallback to localStorage or default
    const theme = document.documentElement.dataset.theme || localStorage.getItem('theme') || 'light';
    applyTheme(theme);
});

/**
 * Listen for Livewire theme-changed event
 */
document.addEventListener('livewire:init', () => {
    Livewire.on('theme-changed', (event) => {
        applyTheme(event.theme);
    });
});
