import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from 'tailwindcss';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/views/themes/tailwind/assets/sass/app.scss',
                'resources/views/themes/tailwind/assets/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
