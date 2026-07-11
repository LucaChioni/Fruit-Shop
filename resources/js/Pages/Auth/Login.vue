<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const t = useTranslations();

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
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
            <div class="mb-6 rounded-lg bg-orange-50 p-4 text-sm text-orange-900">
                {{ t('auth.login_help', "Il checkout ospite resta disponibile. L'accesso serve per ritrovare gli ordini effettuati.") }}
            </div>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
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

                <div class="mt-4">
                    <InputLabel for="password" :value="t('auth.password', 'Password')" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 block">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600"
                            >{{ t('auth.remember', 'Ricordami') }}</span
                        >
                    </label>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ t('auth.forgot_password', 'Password dimenticata?') }}
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ t('auth.login', 'Accedi') }}
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
