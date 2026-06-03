import './bootstrap';

import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Planora';

createInertiaApp({
    // Sets the document <title> for each page: "<page> - Planora".
    title: (title) => (title ? `${title} - ${appName}` : appName),

    // Maps an Inertia page name (e.g. "Products/Index") to its .vue file.
    // import.meta.glob lets Vite code-split each page into its own bundle.
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),

    // Boots the Vue app and mounts it into the @inertia element from app.blade.php.
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },

    // The thin loading bar shown during Inertia navigations.
    progress: {
        color: '#4f46e5',
    },
});
