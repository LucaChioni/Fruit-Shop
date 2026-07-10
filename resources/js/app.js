import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, Fragment, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import CookieBanner from './Components/CookieBanner.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Il Giardino della Frutta';
const themeStorageKey = 'fruit_shop_theme';

function getPreferredTheme() {
    try {
        const storedTheme = window.localStorage.getItem(themeStorageKey);

        if (storedTheme === 'dark' || storedTheme === 'light') {
            return storedTheme;
        }
    } catch {
        // Fall back to the system preference when localStorage is unavailable.
    }

    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;
}

function setDocumentLocale(locale) {
    document.documentElement.lang = locale === 'it' ? 'it-IT' : 'en-US';
}

applyTheme(getPreferredTheme());

router.on('navigate', (event) => {
    setDocumentLocale(event.detail.page.props.locale);
});

createInertiaApp({
    title: (title) => title || appName,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        setDocumentLocale(props.initialPage.props.locale);

        return createApp({ render: () => h(Fragment, [h(App, props), h(CookieBanner)]) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
