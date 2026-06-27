<script setup>
import PageNav from '@/Components/PageNav.vue';
import OrderList from '@/Components/OrderList.vue';

defineProps({
    orders: Array,
});
</script>

<template>
    <main class="admin-orders-page">
        <header class="admin-orders-header">
            <h1 class="admin-orders-title">Ordini ricevuti</h1>

            <PageNav />
        </header>

        <section v-if="orders.length === 0" class="empty-orders">
            <p>Non ci sono ancora ordini.</p>
        </section>

        <OrderList
            :orders="orders"
            empty-message="Non hai ancora effettuato ordini."
            :show-customer="true"
            detail-route-name="admin.orders.show"
        />
    </main>
</template>

<style scoped>
.admin-orders-page {
    padding: 40px;
}

.admin-orders-header {
    margin-bottom: 24px;
}

.admin-orders-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}
</style>