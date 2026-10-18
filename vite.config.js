import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        react(),
        laravel({
            input: [
                'resources/css/ises.css',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/ises.tsx',
                'resources/js/ises-map-controls.tsx',
            ],
            refresh: true,
        }),
    ],
});
