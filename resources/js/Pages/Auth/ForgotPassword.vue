<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const t = useTranslations();

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <PageContainer narrow>
        <Head title="Il Giardino della Frutta" />

        <header class="page-header auth-header">
            <h1>Il Giardino della Frutta</h1>
            <PageNav />
        </header>

        <section class="auth-card">
            <div class="mb-4 text-sm text-gray-600">
                {{ t('auth.forgot_password_text', 'Hai dimenticato la password? Nessun problema. Inserisci la tua email e ti invieremo un link per crearne una nuova.') }}
            </div>

            <div
                v-if="status"
                class="mb-4 text-sm font-medium text-green-600"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" :value="t('legal.email', 'Email')" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ t('auth.email_password_reset_link', 'Invia link di reset') }}
                    </PrimaryButton>
                </div>
            </form>
        </section>
    </PageContainer>
</template>

<style scoped>
.auth-header {
    margin-bottom: 24px;
}

.auth-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.auth-card {
    width: min(480px, 100%);
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}
</style>
