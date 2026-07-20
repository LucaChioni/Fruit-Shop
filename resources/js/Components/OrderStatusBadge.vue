<script setup>
import { useTranslations } from '@/i18n';

defineProps({
    status: {
        type: String,
        required: true,
    },
});

const t = useTranslations();
</script>

<template>
    <span class="order-status" :class="`order-status--${status}`">
        {{ t(`orders.${status}`, status) }}
    </span>
</template>

<style scoped>
.order-status {
    display: inline-flex;
    align-items: center;
    margin-left: 3px;
    padding: 2px 8px;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.3;
}

.order-status--pending {
    border-color: #fde68a;
    background: #fef3c7;
    color: #92400e;
}

.order-status--ready {
    border-color: #bbf7d0;
    background: #dcfce7;
    color: #166534;
}

.order-status--completed {
    border-color: #e5e7eb;
    background: #f3f4f6;
    color: #374151;
}

.order-status--cancelled {
    border-color: #fecaca;
    background: #fee2e2;
    color: #991b1b;
}
</style>
