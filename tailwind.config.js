import preset from './vendor/filament/support/tailwind.config.preset'
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    presets: [preset],
    darkMode: 'class',
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                /* Premium dark theme surface colors */
                surface: {
                    DEFAULT: '#0b0f14',
                    50: '#141a22',
                    100: '#1a2230',
                    200: '#1e2736',
                    300: '#243040',
                    400: '#2c3a4d',
                    500: '#374b63',
                },
                /* Accent mint/cyan/teal */
                accent: {
                    DEFAULT: '#5eead4',
                    50: '#0d3d38',
                    100: '#115e4a',
                    200: '#14b8a6',
                    300: '#2dd4bf',
                    400: '#5eead4',
                    500: '#99f6e4',
                    600: '#ccfbf1',
                },
            },
            spacing: {
                '4.5': '1.125rem',
                '18': '4.5rem',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            boxShadow: {
                'soft': '0 2px 8px 0 rgba(0, 0, 0, 0.15)',
                'soft-lg': '0 4px 16px 0 rgba(0, 0, 0, 0.25)',
                'soft-xl': '0 8px 24px 0 rgba(0, 0, 0, 0.35)',
                'glow-sm': '0 0 10px rgba(94, 234, 212, 0.08)',
                'glow': '0 0 20px rgba(94, 234, 212, 0.12)',
                'glow-lg': '0 0 40px rgba(94, 234, 212, 0.15)',
                'card': '0 4px 24px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.04)',
                'card-hover': '0 8px 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(94, 234, 212, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.06)',
            },
        },
    },
}
