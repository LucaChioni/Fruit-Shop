<script setup>
import OrderList from '@/Components/OrderList.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import SortDirectionButton from '@/Components/SortDirectionButton.vue';
import { submitFilterForm } from '@/filterForm';
import { useTranslations } from '@/i18n';

const props = defineProps({
    orders: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    orderStatuses: {
        type: Array,
        required: true,
    },
    admin: {
        type: Boolean,
        default: false,
    },
});

const t = useTranslations();
</script>

<template>
    <PageContainer>
        <header class="page-list-header" :class="admin ? 'admin-orders-header' : 'orders-header'">
            <PageNav />
        </header>

        <form
            :action="route(admin ? 'admin.orders.index' : 'orders.index')"
            method="get"
            class="filters-form"
            @change="submitFilterForm"
            @submit.prevent="submitFilterForm"
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

            <div class="sort-controls">
                <label class="filter-field filter-field--sort">
                    {{ t('orders.sort', 'Ordina') }}
                    <select name="sort" class="filter-input">
                        <option value="created_at" :selected="filters.sort === 'created_at'">{{ t('orders.sort_created_at', 'Data ordine') }}</option>
                        <option value="total_amount" :selected="filters.sort === 'total_amount'">{{ t('orders.sort_total', 'Totale') }}</option>
                    </select>
                </label>

                <input type="hidden" name="sort_direction" :value="filters.sort_direction" />

                <SortDirectionButton
                    :direction="filters.sort_direction"
                    :ascending-label="t('products.sort_asc', 'Ascendente')"
                    :descending-label="t('products.sort_desc', 'Discendente')"
                    :admin="admin"
                />
            </div>
        </form>

        <OrderList
            :orders="orders"
            :empty-message="t('orders.empty', 'Non ci sono ancora ordini.')"
            :detail-route-name="admin ? 'admin.orders.show' : 'orders.show'"
        />
    </PageContainer>
</template>

<style scoped>
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
    height: 36px;
    padding: 7px 9px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font: inherit;
}

.sort-controls {
    display: flex;
    flex: 1 1 226px;
    align-items: end;
    gap: 8px;
    min-width: min(226px, 100%);
}

.filter-field--sort {
    flex: 1 1 auto;
    min-width: 0;
}

@media (max-width: 640px) {
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
