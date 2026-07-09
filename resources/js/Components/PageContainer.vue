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
    padding: 40px 24px;
}

.page-container :deep(.legal-footer) {
    margin-top: auto;
}

.page-container--narrow {
    width: min(1120px, calc(100% - 32px));
    margin: 0 auto;
    padding: 40px 0;
}

.page-container--narrow :deep(.legal-header) {
    box-sizing: border-box;
    width: 100vw;
    margin-right: calc(50% - 50vw);
    margin-left: calc(50% - 50vw);
    padding: 0 24px;
}

@media (max-width: 640px) {
    .page-container:not(.page-container--narrow) {
        padding: 32px 16px;
    }

    .page-container--narrow :deep(.legal-header) {
        padding: 0 16px;
    }
}
</style>
