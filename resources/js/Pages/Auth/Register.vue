<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const t = useTranslations();

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <PageContainer narrow>
        <Head title="Il Giardino della Frutta" />

        <header class="page-header auth-header">
            <PageNav />
        </header>

        <section class="auth-card">
            <div class="auth-help mb-4 rounded-lg text-sm">
                {{ t('auth.register_help', 'Crea un account per aggiungere prodotti al carrello e completare gli ordini.') }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" :value="t('auth.name', 'Nome')" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" :value="t('legal.email', 'Email')" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" :value="t('auth.password', 'Password')" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        :value="t('auth.confirm_password', 'Conferma password')"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        :href="route('login')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ t('auth.already_registered', 'Hai già un account?') }}
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ t('auth.register', 'Registrati') }}
                    </PrimaryButton>
                </div>
            </form>
        </section>
    </PageContainer>
</template>

<style scoped>
.auth-header {
    margin-bottom: 16px;
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

:global(html.dark) .auth-help {
    border-color: #334155;
    background: #111827;
    color: #cbd5e1;
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
}
</style>
