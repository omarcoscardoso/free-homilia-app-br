import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    base: '/',
    build: {
        outDir: 'public/build'
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            strategies: 'generateSW',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            
            registerType: 'autoUpdate',
            injectRegister: 'auto',

            srcDir: 'public',
            outDir: 'public/build',

            workbox: {
                navigateFallback: '/',
                globPatterns: [
                    '**/*.{js,css,html,ico,png,svg}',
                    '../images/icons/*.png',
                ],
                // regra de cache de tempo de execução
                runtimeCaching: [
                    {
                        urlPattern: ({ url }) => url.pathname === '/',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'home-page-cache',
                        },
                    },
                ],
            },
            
            // Configurações do manifesto PWA.
            manifest: {
                name: 'HomilIA App',
                short_name: 'Homilia',
                description: 'Um aplicativo que te ajuda a gerar esboço de sermão.',
                lang: 'pt-BR',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                background_color: '#ffffff',
                theme_color: '#1F2937',
                icons: [
                    {
                        src: '../images/icons/icon-72x72.png',
                        sizes: '72x72',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '../images/icons/icon-96x96.png',
                        sizes: '96x96',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '../images/icons/icon-128x128.png',
                        sizes: '128x128',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '../images/icons/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '../images/icons/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable any'
                    }
                ]
            }
        })
    ],
});