import { usePage } from '@inertiajs/vue3';

export function useTranslations() {
    const page = usePage();

    return (key, fallback = key, replacements = {}) => {
        let translation = page.props.translations?.[key] ?? fallback;

        for (const [placeholder, value] of Object.entries(replacements)) {
            translation = translation.replaceAll(`:${placeholder}`, String(value));
        }

        return translation;
    };
}
