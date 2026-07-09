<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { useTranslations } from '@/i18n';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const t = useTranslations();

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ t('settings.delete_account', 'Elimina account') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ t('settings.delete_account_text', "Una volta eliminato l'account, tutte le risorse e i dati associati verranno eliminati definitivamente.") }}
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">{{ t('settings.delete_account', 'Elimina account') }}</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900"
                >
                    {{ t('settings.delete_account_confirm_title', 'Sei sicuro di voler eliminare il tuo account?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ t('settings.delete_account_confirm_text', "Questa operazione è definitiva. Inserisci la password per confermare l'eliminazione dell'account.") }}
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        :value="t('auth.password', 'Password')"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        :placeholder="t('auth.password', 'Password')"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        {{ t('settings.cancel', 'Annulla') }}
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ t('settings.delete_account', 'Elimina account') }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
