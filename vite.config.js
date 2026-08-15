import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Layouts reference build/assets/app.js|css directly (no manifest
        // lookup), so keep output filenames stable across builds instead of
        // content-hashing them.
        rollupOptions: {
            output: {
                entryFileNames: (chunk) => {
                    // The CSS-only entry (resources/css/app.css) also emits
                    // an auxiliary JS chunk; give it a distinct name so it
                    // can't collide with — and silently displace — the real
                    // resources/js/app.js bundle at assets/app.js.
                    if (chunk.facadeModuleId && chunk.facadeModuleId.endsWith('.css')) {
                        return 'assets/app-style-entry.js';
                    }
                    return 'assets/[name].js';
                },
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]',
            },
        },
    },
});
