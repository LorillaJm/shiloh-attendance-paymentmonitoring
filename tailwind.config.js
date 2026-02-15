import preset from './vendor/filament/support/tailwind.config.preset'
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
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
                'soft': '0 2px 8px 0 rgba(0, 0, 0, 0.05)',
                'soft-lg': '0 4px 16px 0 rgba(0, 0, 0, 0.08)',
                'soft-xl': '0 8px 24px 0 rgba(0, 0, 0, 0.1)',
            },
        },
    },
}
