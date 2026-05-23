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
                sans: ['-apple-system', 'BlinkMacSystemFont', '"SF Pro Display"', '"SF Pro Text"', '"Helvetica Neue"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                /* Apple Design System */
                apple: {
                    blue: '#0071E3',
                    'blue-hover': '#006EDB',
                    'blue-active': '#0076DF',
                },
                surface: {
                    DEFAULT: '#FAFAFC',
                    50: '#FFFFFF',
                    100: '#FAFAFC',
                    200: '#F5F5F7',
                    300: '#EDEDF2',
                    400: '#D2D2D7',
                    500: '#AEAEB2',
                },
                accent: {
                    DEFAULT: '#0071E3',
                    50: '#EBF5FF',
                    100: '#D6EBFF',
                    200: '#ADD6FF',
                    300: '#7ABCFF',
                    400: '#3399FF',
                    500: '#0071E3',
                    600: '#006EDB',
                },
            },
            spacing: {
                '4.5': '1.125rem',
                '18': '4.5rem',
            },
            borderRadius: {
                'apple-sm': '8px',
                'apple-md': '12px',
                'apple-lg': '18px',
                'apple-xl': '28px',
            },
            boxShadow: {
                'apple-sm': '0 1px 3px rgba(0, 0, 0, 0.04)',
                'apple': '0 2px 8px rgba(0, 0, 0, 0.08)',
                'apple-lg': '0 4px 12px rgba(0, 0, 0, 0.1)',
                'apple-xl': '0 8px 24px rgba(0, 0, 0, 0.12)',
                'apple-hover': '0 2px 8px rgba(0, 0, 0, 0.08)',
                'apple-modal': '0 16px 40px rgba(0, 0, 0, 0.16)',
                'apple-focus': '0 0 0 3px rgba(0, 113, 227, 0.1)',
            },
        },
    },
}
