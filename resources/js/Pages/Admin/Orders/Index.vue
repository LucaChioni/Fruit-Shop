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

function toggleSortDirection(event) {
    const formElement = event.currentTarget.form;
    const directionInput = formElement?.querySelector('input[name="sort_direction"]');

    if (! formElement || ! directionInput) {
        return;
    }

    directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
    formElement.requestSubmit();
}
</script>

<template>
    <PageContainer>
        <header class="admin-orders-header">
            <PageNav />

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

            <div class="sort-controls">
                <label class="filter-field filter-field--sort">
                    {{ t('orders.sort', 'Ordina') }}
                    <select name="sort" class="filter-input">
                        <option value="created_at" :selected="filters.sort === 'created_at'">{{ t('orders.sort_created_at', 'Data ordine') }}</option>
                        <option value="total_amount" :selected="filters.sort === 'total_amount'">{{ t('orders.sort_total', 'Totale') }}</option>
                    </select>
                </label>

                <input type="hidden" name="sort_direction" :value="filters.sort_direction" />

                <button
                    type="button"
                    class="sort-direction-button"
                    :aria-label="filters.sort_direction === 'asc' ? t('products.sort_asc', 'Ascendente') : t('products.sort_desc', 'Discendente')"
                    :title="filters.sort_direction === 'asc' ? t('products.sort_asc', 'Ascendente') : t('products.sort_desc', 'Discendente')"
                    @click="toggleSortDirection"
                >
                    <svg class="sort-direction-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <template v-if="filters.sort_direction === 'asc'">
                            <path d="M12 19V5" />
                            <path d="m6 11 6-6 6 6" />
                        </template>
                        <template v-else>
                            <path d="M12 5v14" />
                            <path d="m6 13 6 6 6-6" />
                        </template>
                    </svg>
                </button>
            </div>
        </form>

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

.sort-direction-button {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 34px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    color: #7c2d12;
    cursor: pointer;
}

.sort-direction-button:hover,
.sort-direction-button:focus-visible {
    border-color: #9a3412;
    background: #fff7ed;
    outline: none;
}

.sort-direction-icon {
    width: 18px;
    height: 18px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
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
