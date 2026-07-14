<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslations } from '@/i18n';

const confirmingUserDeletion = ref(false);
const t = useTranslations();

const form = useForm({});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
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
                    {{ t('settings.delete_account_confirm_text', "Questa operazione è definitiva e non potrà essere annullata.") }}
                </p>

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
