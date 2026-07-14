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
                    {{ t('orders.order', 'Ordine') }} {{ order.order_number ?? '#' + order.id }}
                </h2>

                <p class="order-card-meta">
                    {{ order.created_at }} · {{ t('orders.status', 'Stato') }}:
                    <span class="order-status" :class="`order-status--${order.status}`">
                        {{ t(`orders.${order.status}`, order.status) }}
                    </span>
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

.customer-type {
    display: inline-flex;
    margin-left: 6px;
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

@media (max-width: 640px) {
    .empty-orders,
    .order-card {
        padding: 10px;
    }

    .orders-list {
        gap: 10px;
    }

    .order-card {
        gap: 8px;
    }

    .order-card-title {
        font-size: 17px;
    }
}
</style>
