import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/apple-dashboard.css',
                'resources/css/dark-mode-improvements.css',
                'resources/css/responsive.css',
                'resources/css/filament/admin/theme.css',
                'resources/js/app.js',
                'resources/js/apple-dashboard.js',
                'resources/js/theme-toggle.js'
            ],
            refresh: true,
        }),
    ],
});
