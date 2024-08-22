import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ReportGenerator from './Components/ReportGenerator.vue';
import UploadForm from './Pages/UploadForm.vue';
import router from './router';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
InertiaProgress.init();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(router)
            .component('UploadForm', UploadForm)
            .component('ReportGenerator', ReportGenerator)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
