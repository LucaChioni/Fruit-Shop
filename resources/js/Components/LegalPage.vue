<script setup>
import LegalDocument from '@/Components/LegalDocument.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';

defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['privacy', 'cookies', 'terms'].includes(value),
    },
});

</script>

<template>
    <PageContainer narrow>
        <header class="legal-header page-header">
            <PageNav />
        </header>

        <section class="legal-card">
            <LegalDocument :type="type" />
        </section>
    </PageContainer>
</template>

<style scoped>
.legal-header {
    margin-bottom: 16px;
}

.legal-card {
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

@media (max-width: 640px) {
    .legal-header {
        margin-bottom: 12px;
    }

    .legal-card {
        padding: 10px;
    }
}
</style>
