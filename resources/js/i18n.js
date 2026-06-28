import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();

    return (key, fallback = key) => page.props.translations?.[key] ?? fallback;
}
