<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    status: String,
    emailLoginEmail: String,
    emailLoginNeedsName: Boolean,
});

const form = useForm({
    email: props.emailLoginEmail ?? '',
    name: '',
    code: '',
});

const t = useTranslations();
const codeSent = computed(() => Boolean(props.emailLoginEmail || form.errors.code));

const submit = () => {
    form.post(codeSent.value ? route('login.verify') : route('login'));
};

function setLoginValidationMessage(event) {
    const input = event.currentTarget;

    if (input.validity.valueMissing) {
        input.setCustomValidity(t('validation.required_field', 'Compila questo campo.'));
        return;
    }

    if (input.validity.typeMismatch) {
        input.setCustomValidity(t('validation.email_valid', 'Inserisci un indirizzo email valido.'));
        return;
    }

    input.setCustomValidity('');
}

function clearLoginValidationMessage(event) {
    event.currentTarget.setCustomValidity('');
}
</script>

<template>
    <PageContainer narrow>
        <Head title="Il Giardino della Frutta" />

        <header class="page-header auth-header">
            <PageNav />
        </header>

        <div class="auth-content">
            <section class="auth-card">
                <div class="auth-help mb-4 rounded-lg text-sm">
                    {{ t('auth.login_help', 'Inserisci la tua email: ti invieremo un codice per accedere. Se non hai ancora un account, lo creeremo automaticamente.') }}
                </div>

                <div v-if="status" class="auth-status mb-4 rounded-lg text-sm">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div v-if="!codeSent">
                        <InputLabel for="email" :value="t('legal.email', 'Email')" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            :readonly="codeSent"
                            autofocus
                            autocomplete="username"
                            @invalid="setLoginValidationMessage"
                            @input="clearLoginValidationMessage"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="codeSent && emailLoginNeedsName">
                        <InputLabel for="name" :value="t('auth.name', 'Nome')" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            @invalid="setLoginValidationMessage"
                            @input="clearLoginValidationMessage"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div v-if="codeSent" class="mt-4">
                        <InputLabel for="code" :value="t('auth.email_code', 'Codice ricevuto via email')" />

                        <TextInput
                            id="code"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.code"
                            required
                            inputmode="numeric"
                            autocomplete="one-time-code"
                        />

                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div class="mt-4 flex items-center justify-end">
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ codeSent ? t('auth.verify_code', 'Verifica codice') : t('auth.login', 'Accedi') }}
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </div>
    </PageContainer>
</template>

<style scoped>
.auth-header {
    margin-bottom: 16px;
}

.auth-content {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: center;
}

.auth-card {
    width: min(480px, 100%);
    margin: 0 auto;
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.auth-help {
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #374151;
}

.auth-status {
    padding: 10px 12px;
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
    color: #166534;
}

:global(html.dark) .auth-status {
    border-color: #14532d;
    background: #052e16;
    color: #86efac;
}

@media (max-width: 640px) {
    .auth-header {
        margin-bottom: 12px;
    }

    .auth-card {
        padding: 10px;
    }

    .auth-help {
        padding: 8px;
    }

    .auth-status {
        padding: 8px;
    }
}
</style>
