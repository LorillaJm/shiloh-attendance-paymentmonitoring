// Theme Toggle System for Parent Portal
// Modern SaaS Dashboard Theme Switcher

(function() {
    'use strict';

    // Theme Manager
    const ThemeManager = {
        STORAGE_KEY: 'parent-portal-theme',
        THEMES: {
            LIGHT: 'light',
            DARK: 'dark'
        },

        // Initialize theme system
        init() {
            this.loadTheme();
            this.setupToggle();
            this.watchSystemPreference();
        },

        // Get current theme
        getCurrentTheme() {
            const stored = localStorage.getItem(this.STORAGE_KEY);
            if (stored) {
                return stored;
            }

            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return this.THEMES.DARK;
            }

            return this.THEMES.LIGHT;
        },

        // Set theme
        setTheme(theme) {
            const html = document.documentElement;
            
            if (theme === this.THEMES.DARK) {
                html.setAttribute('data-theme', 'dark');
                html.classList.add('dark');
            } else {
                html.setAttribute('data-theme', 'light');
                html.classList.remove('dark');
            }

            localStorage.setItem(this.STORAGE_KEY, theme);
            this.updateToggleButton(theme);
        },

        // Toggle theme
        toggleTheme() {
            const current = this.getCurrentTheme();
            const next = current === this.THEMES.LIGHT ? this.THEMES.DARK : this.THEMES.LIGHT;
            this.setTheme(next);
        },

        // Load theme on page load
        loadTheme() {
            const theme = this.getCurrentTheme();
            this.setTheme(theme);
        },

        // Setup toggle button
        setupToggle() {
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggleTheme());
            }
        },

        // Update toggle button icon
        updateToggleButton(theme) {
            const toggleBtn = document.getElementById('theme-toggle');
            if (!toggleBtn) return;

            const sunIcon = toggleBtn.querySelector('.sun-icon');
            const moonIcon = toggleBtn.querySelector('.moon-icon');

            if (theme === this.THEMES.DARK) {
                if (sunIcon) sunIcon.style.display = 'block';
                if (moonIcon) moonIcon.style.display = 'none';
            } else {
                if (sunIcon) sunIcon.style.display = 'none';
                if (moonIcon) moonIcon.style.display = 'block';
            }
        },

        // Watch for system preference changes
        watchSystemPreference() {
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addEventListener('change', (e) => {
                    // Only auto-switch if user hasn't set a preference
                    if (!localStorage.getItem(this.STORAGE_KEY)) {
                        this.setTheme(e.matches ? this.THEMES.DARK : this.THEMES.LIGHT);
                    }
                });
            }
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ThemeManager.init());
    } else {
        ThemeManager.init();
    }

    // Expose to window for manual control
    window.ThemeManager = ThemeManager;

})();

// Smooth scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll to all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation to cards
    const cards = document.querySelectorAll('.card, .stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Add ripple CSS
const style = document.createElement('style');
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Smooth transitions for theme switching */
    * {
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    /* Prevent transition on page load */
    .no-transition * {
        transition: none !important;
    }
`;
document.head.appendChild(style);

// Prevent flash of unstyled content
document.documentElement.classList.add('no-transition');
window.addEventListener('load', () => {
    setTimeout(() => {
        document.documentElement.classList.remove('no-transition');
    }, 100);
});
