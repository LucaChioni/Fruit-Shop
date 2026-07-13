<script setup>
import LegalFooter from '@/Components/LegalFooter.vue';

defineProps({
    narrow: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <main class="page-container" :class="{ 'page-container--narrow': narrow }">
        <slot />
        <LegalFooter />
    </main>
</template>

<style scoped>
.page-container {
    box-sizing: border-box;
    display: flex;
    min-height: 100vh;
    flex-direction: column;
    width: 100%;
    padding: 28px 24px;
}

.page-container :deep(.legal-footer) {
    margin-top: auto;
}

.page-container > :not(.legal-footer):last-of-type {
    margin-bottom: 40px;
}

.page-container--narrow {
    width: min(1120px, calc(100% - 32px));
    margin: 0 auto;
    padding: 28px 0;
}

.page-container--narrow :deep(.legal-header),
.page-container--narrow :deep(.checkout-header),
.page-container--narrow :deep(.order-header),
.page-container--narrow :deep(.product-form-header),
.page-container--narrow :deep(.settings-header),
.page-container--narrow :deep(.page-header) {
    box-sizing: border-box;
    width: 100vw;
    margin-right: calc(50% - 50vw);
    margin-left: calc(50% - 50vw);
    padding: 0 24px;
}

@media (max-width: 640px) {
    .page-container:not(.page-container--narrow) {
        padding: 20px 14px;
    }

    .page-container--narrow :deep(.legal-header),
    .page-container--narrow :deep(.checkout-header),
    .page-container--narrow :deep(.order-header),
    .page-container--narrow :deep(.product-form-header),
    .page-container--narrow :deep(.settings-header),
    .page-container--narrow :deep(.page-header) {
        padding: 0 16px;
    }
}
</style>
