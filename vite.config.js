import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // server: {
    //     host: '0.0.0.0',         // Accept requests from any device
    //     port: 5173,              // Vite port
    //     strictPort: true,
    //     hmr: {
    //         host: '192.168.43.229', // Your laptop IP again
    //     },
    // },
});
