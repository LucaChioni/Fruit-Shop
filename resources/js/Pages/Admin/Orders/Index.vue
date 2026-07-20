<script setup>
import OrdersIndexPage from '@/Components/OrdersIndexPage.vue';

defineProps({
    orders: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    orderStatuses: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <OrdersIndexPage
        :orders="orders"
        :filters="filters"
        :order-statuses="orderStatuses"
        admin
    />
</template>
