<script setup>
import PageNav from '@/Components/PageNav.vue';

defineProps({
    stats: Object,
    latestOrders: Array,
});
</script>

<template>
    <main class="admin-page">
        <header class="admin-header">
            <h1 class="admin-title">Admin</h1>

            <PageNav />
        </header>

        <section class="stats-grid">
            <article class="admin-card">
                <span class="stat-label">Ordini pending</span>
                <strong class="stat-value">{{ stats.pending_orders }}</strong>
            </article>

            <article class="admin-card">
                <span class="stat-label">Ordini di oggi</span>
                <strong class="stat-value">{{ stats.today_orders }}</strong>
            </article>

            <article class="admin-card">
                <span class="stat-label">Ordini totali</span>
                <strong class="stat-value">{{ stats.total_orders }}</strong>
            </article>
        </section>

        <section class="quick-links">
            <a :href="route('admin.orders.index')" class="quick-link">
                Gestisci ordini
            </a>

            <a :href="route('admin.products.index')" class="quick-link">
                Gestisci prodotti
            </a>

            <a :href="route('admin.products.create')" class="quick-link quick-link--primary">
                Nuovo prodotto
            </a>
        </section>

        <section class="admin-card latest-card">
            <h2>Ultimi ordini</h2>

            <p v-if="latestOrders.length === 0" class="empty-message">
                Non ci sono ancora ordini.
            </p>

            <div v-else class="latest-orders">
                <article
                    v-for="order in latestOrders"
                    :key="order.id"
                    class="latest-order"
                >
                    <div>
                        <strong>{{ order.order_number }}</strong>
                        <p>{{ order.customer_name }} · {{ order.status }}</p>
                    </div>

                    <a :href="route('admin.orders.show', order.id)" class="order-link">
                        Dettaglio
                    </a>
                </article>
            </div>
        </section>
    </main>
</template>

<style scoped>
.admin-page {
    padding: 40px;
}

.admin-header {
    margin-bottom: 24px;
}

.admin-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.admin-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.stat-label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 600;
}

.stat-value {
    font-size: 32px;
}

.quick-links {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.quick-link {
    padding: 10px 14px;
    border: 1px solid #7c2d12;
    border-radius: 999px;
    color: #7c2d12;
    font-weight: 700;
    text-decoration: none;
}

.quick-link--primary {
    background: #7c2d12;
    color: #fff;
}

.latest-card h2 {
    margin: 0 0 12px;
}

.latest-orders {
    display: grid;
    gap: 12px;
}

.latest-order {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #eee;
}

.latest-order:last-child {
    padding-bottom: 0;
    border-bottom: 0;
}

.latest-order p,
.empty-message {
    margin: 4px 0 0;
    color: #555;
}

.order-link {
    color: #166534;
    font-weight: 700;
    text-decoration: none;
}

.order-link:hover {
    text-decoration: underline;
}
</style>
