<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="Il giardino della frutta" />

    <PageContainer>
        <header class="settings-header">
            <h1 class="settings-title">Il Giardino della Frutta</h1>
            <PageNav />
        </header>

        <div class="settings-grid">
            <section class="settings-card">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </section>

            <section class="settings-card">
                <UpdatePasswordForm />
            </section>

            <section class="settings-card">
                <DeleteUserForm v-if="canDeleteAccount" />

                <div v-else class="space-y-2">
                    <h2 class="text-lg font-medium text-gray-900">
                        Elimina account
                    </h2>

                    <p class="text-sm text-gray-600">
                        Gli account amministratore non possono essere eliminati.
                    </p>
                </div>
            </section>
        </div>
    </PageContainer>
</template>

<style scoped>
.settings-header {
    margin-bottom: 24px;
}

.settings-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.settings-grid {
    display: grid;
    gap: 16px;
    margin-bottom: 40px;
}

.settings-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}
</style>
