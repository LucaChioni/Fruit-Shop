<script setup>
import AdminNav from '@/Components/AdminNav.vue';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import OrderList from '@/Components/OrderList.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    orders: Array,
    filters: Object,
    orderStatuses: Array,
});

const t = useTranslations();
</script>

<template>
    <PageContainer>
        <header class="admin-orders-header">
            <PageNav />

            <AdminNav />
        </header>

        <form
            :action="route('admin.orders.index')"
            method="get"
            class="filters-form"
            @change="$event.currentTarget.submit()"
        >
            <label class="filter-field">
                {{ t('orders.search', 'Cerca') }}
                <input
                    type="search"
                    name="search"
                    :value="filters.search"
                    class="filter-input"
                    :placeholder="t('orders.search_placeholder', 'Nome o codice ordine')"
                />
            </label>

            <label class="filter-field">
                {{ t('orders.status', 'Stato') }}
                <select name="status" class="filter-input">
                    <option value="all" :selected="filters.status === 'all'">{{ t('orders.all', 'Tutti') }}</option>
                    <option
                        v-for="status in orderStatuses"
                        :key="status"
                        :value="status"
                        :selected="filters.status === status"
                    >
                        {{ t(`orders.${status}`, status) }}
                    </option>
                </select>
            </label>

            <label class="filter-field">
                {{ t('orders.customer', 'Cliente') }}
                <select name="customer_type" class="filter-input">
                    <option value="all" :selected="filters.customer_type === 'all'">{{ t('orders.all', 'Tutti') }}</option>
                    <option value="registered" :selected="filters.customer_type === 'registered'">{{ t('orders.registered', 'Registrati') }}</option>
                    <option value="guest" :selected="filters.customer_type === 'guest'">{{ t('orders.guests', 'Ospiti') }}</option>
                </select>
            </label>

            <label class="filter-field">
                {{ t('orders.sort', 'Ordina') }}
                <select name="sort" class="filter-input">
                    <option value="newest" :selected="filters.sort === 'newest'">{{ t('orders.newest', 'Più recenti') }}</option>
                    <option value="oldest" :selected="filters.sort === 'oldest'">{{ t('orders.oldest', 'Meno recenti') }}</option>
                    <option value="total_desc" :selected="filters.sort === 'total_desc'">{{ t('orders.total_desc', 'Totale decrescente') }}</option>
                    <option value="total_asc" :selected="filters.sort === 'total_asc'">{{ t('orders.total_asc', 'Totale crescente') }}</option>
                </select>
            </label>
        </form>

        <section v-if="orders.length === 0" class="empty-orders">
            <p>{{ t('orders.empty', 'Non ci sono ancora ordini.') }}</p>
        </section>

        <OrderList
            :orders="orders"
            :empty-message="t('orders.empty', 'Non ci sono ancora ordini.')"
            :show-customer="true"
            detail-route-name="admin.orders.show"
        />
    </PageContainer>
</template>

<style scoped>
.admin-orders-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    margin-bottom: 16px;
}

.admin-orders-header :deep(.admin-nav) {
    flex: 1 1 100%;
}

.filters-form {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 10px;
    margin-bottom: 16px;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.filter-field {
    display: grid;
    flex: 1 1 180px;
    gap: 4px;
    font-size: 14px;
    font-weight: 600;
}

.filter-input {
    box-sizing: border-box;
    min-width: min(180px, 100%);
    width: 100%;
    padding: 7px 9px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font: inherit;
}

@media (max-width: 640px) {
    .admin-orders-header {
        gap: 8px;
        margin-bottom: 12px;
    }

    .filters-form {
        gap: 8px;
        margin-bottom: 12px;
        padding: 8px;
    }

    .filter-field {
        flex-basis: 140px;
    }
}
</style>
