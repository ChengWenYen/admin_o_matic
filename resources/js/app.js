require('./bootstrap');

window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Import modules...
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link} from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';

// Import components...
import Multiselect from '@suadelabs/vue3-multiselect';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .component('InertiaHead', Head)
            .component('InertiaLink', Link)
            .component('multiselect', Multiselect)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
