<script setup>
import PageNav from '@/Components/PageNav.vue';
import OrderList from '@/Components/OrderList.vue';

const props = defineProps({
    orders: Array,
});
</script>

<template>
    <main class="orders-page">
        <header class="orders-header">
            <h1 class="orders-title">I miei ordini</h1>

            <PageNav />
        </header>

        <OrderList
            :orders="orders"
            empty-message="Non ci sono ancora ordini."
            :show-customer="true"
            detail-route-name="orders.show"
        />
    </main>
</template>

<style scoped>
.orders-page {
    padding: 40px;
}

.orders-header {
    margin-bottom: 24px;
}

.orders-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}
</style>