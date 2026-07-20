<script setup>
import { Link } from '@inertiajs/vue3';
import OrderStatusBadge from '@/Components/OrderStatusBadge.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
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
                    {{ t('orders.order', 'Ordine') }} {{ order.order_number ?? '#' + order.id }}
                </h2>

                <p class="order-card-meta">
                    {{ order.created_at }} · {{ t('orders.status', 'Stato') }}:
                    <OrderStatusBadge :status="order.status" />
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
            </div>

            <div class="order-card-side">
                <strong>{{ order.total_amount }} €</strong>

                <Link :href="route(detailRouteName, order.id)" class="order-link">
                    {{ t('orders.detail', 'Dettaglio') }}
                </Link>
            </div>
        </article>
    </section>
</template>

<style scoped>
.empty-orders {
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
    color: #555;
}

.orders-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
    gap: 12px;
}

.order-card {
    display: grid;
    gap: 10px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.order-card-title {
    margin: 0 0 5px;
    font-size: 18px;
    font-weight: 700;
}

.order-card-meta,
.order-card-customer {
    margin: 0 0 3px;
    color: #555;
    font-size: 14px;
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

@media (max-width: 640px) {
    .empty-orders {
        padding: 10px;
    }

    .order-card {
        padding: 6px;
    }

    .orders-list {
        grid-template-columns: repeat(auto-fit, minmax(min(250px, 100%), 1fr));
        justify-content: center;
        gap: 4px;
    }

    .order-card {
        gap: 8px;
    }

    .order-card-title {
        font-size: 17px;
    }
}
</style>
