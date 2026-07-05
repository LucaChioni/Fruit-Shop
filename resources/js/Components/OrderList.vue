<script setup>
import { useTranslations } from '@/i18n';

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

const t = useTranslations();
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

                <p v-if="order.pickup_at" class="order-card-meta">
                    {{ t('orders.pickup', 'Ritiro') }}: {{ order.pickup_at }}
                </p>

                <p v-if="showCustomer" class="order-card-customer">
                    {{ t('orders.customer', 'Cliente') }}: {{ order.customer_name }}
                </p>

                <p v-if="showCustomer && order.customer_email" class="order-card-customer">
                    {{ t('orders.email', 'Email') }}: {{ order.customer_email }}
                </p>
                <p v-else class="customer-type" :class="`customer-type-guest`">
                    {{ t('orders.guest', 'Ospite') }}
                </p>
            </div>

            <div class="order-card-side">
                <strong>{{ order.total_amount }} €</strong>

                <a :href="route(detailRouteName, order.id)" class="order-link">
                    {{ t('orders.detail', 'Dettaglio') }}
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
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
}

.order-card {
    display: grid;
    gap: 14px;
    padding: 16px;
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

.customer-type-registered {
    background: #dcfce7;
    color: #166534;
}

.customer-type-guest {
    background: #ffedd5;
    color: #9a3412;
}

.order-card-side {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
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
