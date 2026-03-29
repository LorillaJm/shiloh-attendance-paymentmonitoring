// Modern Theme Toggle System
// Smooth transitions and animations

(function () {
    'use strict';

    const ThemeManager = {
        STORAGE_KEY: 'shiloh-theme',
        THEMES: {
            LIGHT: 'light',
            DARK: 'dark'
        },

        init() {
            this.loadTheme();
            this.setupTransitions();
            this.watchSystemPreference();
            this.listenToLivewireEvents();
        },

        getCurrentTheme() {
            const stored = localStorage.getItem(this.STORAGE_KEY);
            if (stored) return stored;

            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return this.THEMES.DARK;
            }

            return this.THEMES.LIGHT;
        },

        setTheme(theme, animate = true) {
            const html = document.documentElement;
            
            if (animate) {
                // Add transition class
                html.classList.add('theme-transitioning');
                
                // Create ripple effect
                this.createRippleEffect();
            }

            if (theme === this.THEMES.DARK) {
                html.setAttribute('data-theme', 'dark');
                html.classList.add('dark');
            } else {
                html.setAttribute('data-theme', 'light');
                html.classList.remove('dark');
            }

            localStorage.setItem(this.STORAGE_KEY, theme);

            if (animate) {
                setTimeout(() => {
                    html.classList.remove('theme-transitioning');
                }, 600);
            }
        },

        createRippleEffect() {
            const ripple = document.createElement('div');
            ripple.className = 'theme-ripple';
            document.body.appendChild(ripple);

            setTimeout(() => ripple.remove(), 1000);
        },

        setupTransitions() {
            // Add smooth transition styles
            const style = document.createElement('style');
            style.textContent = `
                .theme-transitioning,
                .theme-transitioning * {
                    transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                                color 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                                border-color 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                                box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
                }

                .theme-ripple {
                    position: fixed;
                    top: 50%;
                    right: 2rem;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, transparent 70%);
                    transform: translate(-50%, -50%) scale(0);
                    animation: theme-ripple 0.8s cubic-bezier(0.4, 0, 0.2, 1);
                    pointer-events: none;
                    z-index: 9999;
                }

                @keyframes theme-ripple {
                    0% {
                        transform: translate(-50%, -50%) scale(0);
                        opacity: 1;
                    }
                    100% {
                        transform: translate(-50%, -50%) scale(100);
                        opacity: 0;
                    }
                }

                /* Smooth page load */
                html.loading * {
                    transition: none !important;
                }
            `;
            document.head.appendChild(style);
        },

        loadTheme() {
            const theme = this.getCurrentTheme();
            this.setTheme(theme, false);
        },

        watchSystemPreference() {
            if (window.matchMedia) {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                mediaQuery.addEventListener('change', (e) => {
                    if (!localStorage.getItem(this.STORAGE_KEY)) {
                        this.setTheme(e.matches ? this.THEMES.DARK : this.THEMES.LIGHT);
                    }
                });
            }
        },

        listenToLivewireEvents() {
            document.addEventListener('livewire:init', () => {
                Livewire.on('theme-changed', (event) => {
                    this.setTheme(event.theme);
                });
            });
        }
    };

    // Initialize
    if (document.readyState === 'loading') {
        document.documentElement.classList.add('loading');
        document.addEventListener('DOMContentLoaded', () => {
            ThemeManager.init();
            setTimeout(() => {
                document.documentElement.classList.remove('loading');
            }, 100);
        });
    } else {
        ThemeManager.init();
    }

    window.ThemeManager = ThemeManager;

})();

// Enhanced UI animations
document.addEventListener('DOMContentLoaded', function () {
    // Smooth scroll
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

    // Staggered card animations
    const cards = document.querySelectorAll('.fi-wi-stats-overview-stat, .fi-section');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
