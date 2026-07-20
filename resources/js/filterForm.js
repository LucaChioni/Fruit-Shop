import { router } from '@inertiajs/vue3';

export function submitFilterForm(event) {
    const form = event.currentTarget;

    router.get(form.action, Object.fromEntries(new FormData(form)));
}
