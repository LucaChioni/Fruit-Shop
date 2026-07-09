<script setup>
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
        <header class="orders-header">
            <h1 class="orders-title">Il Giardino della Frutta</h1>

            <PageNav />
        </header>

        <form
            :action="route('orders.index')"
            method="get"
            class="filters-form"
            @change="$event.currentTarget.submit()"
        >
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
                {{ t('orders.sort', 'Ordina') }}
                <select name="sort" class="filter-input">
                    <option value="newest" :selected="filters.sort === 'newest'">{{ t('orders.newest', 'Più recenti') }}</option>
                    <option value="oldest" :selected="filters.sort === 'oldest'">{{ t('orders.oldest', 'Meno recenti') }}</option>
                    <option value="total_desc" :selected="filters.sort === 'total_desc'">{{ t('orders.total_desc', 'Totale decrescente') }}</option>
                    <option value="total_asc" :selected="filters.sort === 'total_asc'">{{ t('orders.total_asc', 'Totale crescente') }}</option>
                </select>
            </label>
        </form>

        <OrderList
            :orders="orders"
            :empty-message="t('orders.empty', 'Non ci sono ancora ordini.')"
            :show-customer="true"
            detail-route-name="orders.show"
        />
    </PageContainer>
</template>

<style scoped>
.orders-header {
    margin-bottom: 24px;
}

.orders-title {
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

</style>
