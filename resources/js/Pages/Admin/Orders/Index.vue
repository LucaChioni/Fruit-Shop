<script setup>
import PageNav from '@/Components/PageNav.vue';
import OrderList from '@/Components/OrderList.vue';

const props = defineProps({
    orders: Array,
    filters: Object,
    orderStatuses: Array,
});
</script>

<template>
    <main class="admin-orders-page">
        <header class="admin-orders-header">
            <h1 class="admin-orders-title">Ordini ricevuti</h1>

            <PageNav />
        </header>

        <form :action="route('admin.orders.index')" method="get" class="filters-form">
            <label class="filter-field">
                Stato
                <select name="status" class="filter-input">
                    <option value="all" :selected="filters.status === 'all'">Tutti</option>
                    <option
                        v-for="status in orderStatuses"
                        :key="status"
                        :value="status"
                        :selected="filters.status === status"
                    >
                        {{ status }}
                    </option>
                </select>
            </label>

            <label class="filter-field">
                Cliente
                <select name="customer_type" class="filter-input">
                    <option value="all" :selected="filters.customer_type === 'all'">Tutti</option>
                    <option value="registered" :selected="filters.customer_type === 'registered'">Registrati</option>
                    <option value="guest" :selected="filters.customer_type === 'guest'">Ospiti</option>
                </select>
            </label>

            <label class="filter-field">
                Ordina
                <select name="sort" class="filter-input">
                    <option value="newest" :selected="filters.sort === 'newest'">Più recenti</option>
                    <option value="oldest" :selected="filters.sort === 'oldest'">Meno recenti</option>
                    <option value="total_desc" :selected="filters.sort === 'total_desc'">Totale decrescente</option>
                    <option value="total_asc" :selected="filters.sort === 'total_asc'">Totale crescente</option>
                </select>
            </label>

            <button type="submit" class="filter-button">Applica</button>
            <a :href="route('admin.orders.index')" class="reset-link">Reset</a>
        </form>

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

.filters-form {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 12px;
    margin-bottom: 24px;
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.filter-field {
    display: grid;
    gap: 6px;
    font-weight: 600;
}

.filter-input {
    min-width: 180px;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font: inherit;
}

.filter-button {
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    background: #7c2d12;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.reset-link {
    color: #7c2d12;
    font-weight: 600;
    text-decoration: none;
}
</style>
