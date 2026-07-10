<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import AdminNav from '@/Components/AdminNav.vue';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    order: Object,
    isAdminView: {
        type: Boolean,
        default: false,
    },
    orderStatuses: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const t = useTranslations();

const statusForm = useForm({
    status: props.order.status,
});

function updateStatus() {
    statusForm.patch(route('admin.orders.status.update', props.order.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="order-header">
            <h1 class="order-title">Il Giardino della Frutta</h1>

            <PageNav />

            <AdminNav v-if="isAdminView" />

            <p class="order-meta">
                {{ t('orders.reference', 'Riferimento') }} {{ order.order_number ?? '#' + order.id }} ·
                {{ order.created_at }} · {{ t('orders.status', 'Stato') }}: {{ t(`orders.${order.status}`, order.status) }}
            </p>

            <p v-if="order.pickup_at" class="order-meta order-meta--pickup">
                {{ t('orders.pickup', 'Ritiro') }}: {{ order.pickup_at }}
            </p>

            <div v-if="page.props.flash?.success" class="flash-message flash-message--success">
                {{ page.props.flash.success }}
            </div>
        </header>

        <section class="order-section">
            <h2>{{ t('orders.customer', 'Cliente') }}</h2>

            <p>{{ order.customer_name }}</p>

            <p v-if="order.notes">
                {{ t('orders.notes', 'Note') }}: {{ order.notes }}
            </p>
        </section>

        <section v-if="isAdminView" class="order-section">
            <h2>{{ t('orders.manage_status', 'Gestione stato') }}</h2>

            <form class="status-form" @submit.prevent="updateStatus">
                <label class="status-label">
                    {{ t('orders.order_status', 'Stato ordine') }}
                    <select v-model="statusForm.status" class="status-select">
                        <option
                            v-for="status in orderStatuses"
                            :key="status"
                            :value="status"
                        >
                            {{ t(`orders.${status}`, status) }}
                        </option>
                    </select>
                </label>

                <button
                    type="submit"
                    class="status-button"
                    :disabled="statusForm.processing"
                >
                    {{ t('orders.update_status', 'Aggiorna stato') }}
                </button>
            </form>

            <p v-if="statusForm.errors.status" class="status-error">
                {{ statusForm.errors.status }}
            </p>
        </section>

        <section class="order-section">
            <h2>{{ t('orders.products', 'Prodotti') }}</h2>

            <div class="order-items">
                <article
                    v-for="item in order.items"
                    :key="item.id"
                    class="order-item"
                >
                    <div>
                        <h3 class="order-item-name">
                            {{ item.product_name }}
                        </h3>

                        <p class="order-item-details">
                            {{ item.quantity }} {{ item.unit_type }}
                            × {{ item.unit_price }} €
                        </p>
                    </div>

                    <strong>{{ item.line_total }} €</strong>
                </article>
            </div>
        </section>

        <footer class="order-total">
            <span>{{ t('cart.total', 'Totale') }}</span>
            <strong>{{ order.total_amount }} €</strong>
        </footer>
    </PageContainer>
</template>

<style scoped>
.order-header {
    margin-bottom: 24px;
}

.order-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.order-meta {
    margin: 0;
    color: #555;
}

.order-meta--pickup {
    margin-top: 6px;
    font-weight: 700;
}

.order-section {
    margin-bottom: 24px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.order-section h2 {
    margin: 0 0 12px;
    font-size: 20px;
}

.order-items {
    display: grid;
    gap: 12px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: 0;
}

.order-item-name {
    margin: 0 0 4px;
    font-size: 18px;
}

.order-item-details {
    margin: 0;
    color: #555;
}

.order-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 24px;
    padding: 20px;
    border-radius: 12px;
    background: #f3f4f6;
    font-size: 20px;
}

.products-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.products-link:hover {
    text-decoration: underline;
}

.flash-message {
    margin-top: 12px;
    font-weight: 600;
}

.flash-message--success {
    color: #15803d;
}

.status-form {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 12px;
}

.status-label {
    display: grid;
    gap: 6px;
    font-weight: 600;
}

.status-select {
    box-sizing: border-box;
    min-width: min(180px, 100%);
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
}

.status-button {
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    background: #7c2d12;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.status-button:hover {
    background: #5f220d;
}

.status-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.status-error {
    margin: 8px 0 0;
    color: #b91c1c;
    font-weight: 600;
}
</style>
