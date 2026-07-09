<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});
const t = useTranslations();

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Il Giardino della Frutta" />

        <div class="mb-4 text-sm text-gray-600">
            {{ t('auth.verify_email_text', "Grazie per la registrazione. Prima di iniziare, verifica il tuo indirizzo email cliccando il link che ti abbiamo inviato. Se non hai ricevuto l'email, possiamo inviarne un'altra.") }}
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600"
            v-if="verificationLinkSent"
        >
            {{ t('auth.verification_link_sent', "Un nuovo link di verifica è stato inviato all'indirizzo email indicato durante la registrazione.") }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t('auth.resend_verification_email', 'Invia di nuovo email di verifica') }}
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >{{ t('nav.logout', 'Logout') }}</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
