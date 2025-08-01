// import { defineConfig } from 'vite'
// import laravel, { refreshPaths } from 'laravel-vite-plugin'

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: [
//                 ...refreshPaths,
//                 'app/Filament/**',
//                 'app/Forms/Components/**',
//                 'app/Livewire/**',
//                 'app/Infolists/Components/**',
//                 'app/Providers/Filament/**',
//                 'app/Tables/Columns/**',
//             ],
//         }),
//     ],
// })

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/scss/style.scss'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
