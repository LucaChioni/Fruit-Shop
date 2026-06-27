<script setup>
defineProps({
    orders: Array,
});
</script>

<template>
    <main class="orders-page">
        <header class="orders-header">
            <h1 class="orders-title">I miei ordini</h1>

            <a :href="route('products.index')" class="products-link">
                Torna ai prodotti
            </a>
        </header>

        <section v-if="orders.length === 0" class="empty-orders">
            <p>Non hai ancora effettuato ordini.</p>
        </section>

        <section v-else class="orders-list">
            <article
                v-for="order in orders"
                :key="order.id"
                class="order-card"
            >
                <div>
                    <h2 class="order-card-title">
                        Ordine #{{ order.id }}
                    </h2>

                    <p class="order-card-meta">
                        {{ order.created_at }} · Stato: {{ order.status }}
                    </p>

                    <p class="order-card-customer">
                        Cliente: {{ order.customer_name }}
                    </p>
                </div>

                <div class="order-card-side">
                    <strong>{{ order.total_amount }} €</strong>

                    <a :href="route('orders.show', order.id)" class="order-link">
                        Dettaglio
                    </a>
                </div>
            </article>
        </section>
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

.products-link,
.order-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.products-link:hover,
.order-link:hover {
    text-decoration: underline;
}

.empty-orders {
    padding: 24px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
    color: #555;
}

.orders-list {
    display: grid;
    gap: 16px;
}

.order-card {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.order-card-title {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 700;
}

.order-card-meta,
.order-card-customer {
    margin: 0 0 4px;
    color: #555;
}

.order-card-side {
    display: grid;
    gap: 8px;
    justify-items: end;
}
</style>