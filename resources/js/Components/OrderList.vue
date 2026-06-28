<script setup>
const props = defineProps({
    orders: Array,
    emptyMessage: {
        type: String,
        default: 'Non ci sono ordini.',
    },
    showCustomer: {
        type: Boolean,
        default: true,
    },
    detailRouteName: {
        type: String,
        default: 'orders.show',
    },
});
</script>

<template>
    <section v-if="orders.length === 0" class="empty-orders">
        <p>{{ emptyMessage }}</p>
    </section>

    <section v-else class="orders-list">
        <article
            v-for="order in orders"
            :key="order.id"
            class="order-card"
        >
            <div>
                <h2 class="order-card-title">
                    Ordine {{ order.order_number ?? '#' + order.id }}
                </h2>

                <p class="order-card-meta">
                    {{ order.created_at }} · Stato: {{ order.status }}
                </p>

                <p v-if="showCustomer" class="order-card-customer">
                    Cliente: {{ order.customer_name }}
                    <span
                        v-if="order.customer_type_label"
                        class="customer-type"
                        :class="`customer-type--${order.customer_type}`"
                    >
                        {{ order.customer_type_label }}
                    </span>
                </p>

                <p v-if="showCustomer && order.customer_email" class="order-card-customer">
                    Email: {{ order.customer_email }}
                </p>
            </div>

            <div class="order-card-side">
                <strong>{{ order.total_amount }} €</strong>

                <a :href="route(detailRouteName, order.id)" class="order-link">
                    Dettaglio
                </a>
            </div>
        </article>
    </section>
</template>

<style scoped>
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

.customer-type {
    display: inline-flex;
    margin-left: 8px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.customer-type--registered {
    background: #dcfce7;
    color: #166534;
}

.customer-type--guest {
    background: #ffedd5;
    color: #9a3412;
}

.order-card-side {
    display: grid;
    gap: 8px;
    justify-items: end;
}

.order-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.order-link:hover {
    text-decoration: underline;
}
</style>
