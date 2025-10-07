import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command, mode }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/scss/style.scss'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    // Production-oriented build tweaks to produce slimmer/minified bundles
    build: {
        // Use terser for smaller output and configure aggressive compression
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
            format: {
                comments: false,
            },
        },

        // Minify CSS and allow code-splitting for CSS
        cssCodeSplit: true,
        cssMinify: true,

        // Do not produce source maps in production builds
        sourcemap: false,

        // Faster builds, no need to calculate brotli sizes
        brotliSize: false,

        // Inline small assets (default 4096) - keep small to reduce bundle bloat
        assetsInlineLimit: 4096,

        // Create a vendor chunk to better cache large node_modules code
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
    },
}));
