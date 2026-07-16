<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    canDeleteAccount: {
        type: Boolean,
    },
});

const t = useTranslations();
</script>

<template>
    <Head title="Il giardino della frutta" />

    <PageContainer narrow>
        <header class="settings-header">
            <PageNav />
        </header>

        <div class="settings-content">
            <div class="settings-grid">
                <section class="settings-card">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </section>

                <section class="settings-card">
                    <DeleteUserForm v-if="canDeleteAccount" />

                    <div v-else class="space-y-2">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ t('settings.delete_account', 'Elimina account') }}
                        </h2>

                        <p class="text-sm text-gray-600">
                            {{ t('settings.admin_delete_disabled', 'Gli account amministratore non possono essere eliminati.') }}
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </PageContainer>
</template>

<style scoped>
.settings-header {
    margin-bottom: 16px;
}

.settings-content {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: center;
}

.settings-grid {
    display: grid;
    gap: 12px;
}

.settings-card {
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.settings-card :deep(h2) {
    margin-bottom: 0;
    font-size: 18px;
}

.settings-card :deep(form) {
    margin-top: 16px;
}

.settings-card :deep(.space-y-6 > :not([hidden]) ~ :not([hidden])) {
    --tw-space-y-reverse: 0;
    margin-top: calc(16px * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(16px * var(--tw-space-y-reverse));
}

@media (max-width: 640px) {
    .settings-header {
        margin-bottom: 12px;
    }

    .settings-grid {
        gap: 10px;
    }

    .settings-card {
        padding: 10px;
    }

    .settings-card :deep(form) {
        margin-top: 12px;
    }

    .settings-card :deep(.space-y-6 > :not([hidden]) ~ :not([hidden])) {
        --tw-space-y-reverse: 0;
        margin-top: calc(12px * calc(1 - var(--tw-space-y-reverse)));
        margin-bottom: calc(12px * var(--tw-space-y-reverse));
    }
}
</style>
