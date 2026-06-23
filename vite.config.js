import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import viteCompression from 'vite-plugin-compression';
export default defineConfig({
    base: './',
    plugins: [
        laravel({
            buildDirectory: "../../public_html/build",
            input:['resources/css/app.css', 'resources/js/app.js'],
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
        viteCompression({
            algorithm: 'brotliCompress',
            ext: '.br',
            threshold: 10240,
            deleteOriginFile: false,
        })
    ],
    build: {
        minify: true,   
        cssCodeSplit: true,   
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    optimizeDeps: {
        exclude: [
            "@meforma/vue-toaster"
        ],
    },
});
