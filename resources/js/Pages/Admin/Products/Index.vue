<script setup>
import { router } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

defineProps({
    products: Array,
    filters: Object,
});

const t = useTranslations();

function deleteProduct(product) {
    if (!confirm(t('admin.delete_product_confirm', 'Eliminare :name?').replace(':name', product.name))) {
        return;
    }

    router.delete(route('admin.products.destroy', product.id), {
        preserveScroll: true,
    });
}

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
        <header class="admin-products-header">
            <PageNav />

            <FlashMessage />
        </header>

        <div class="filters-row">
            <a :href="route('admin.products.create')" class="create-product-link">
                {{ t('admin.new_product', 'Aggiungi prodotto') }}
            </a>

            <form
                :action="route('admin.products.index')"
                method="get"
                class="filters-form"
                @change="$event.currentTarget.submit()"
            >
                <label class="filter-field">
                    {{ t('products.search', 'Cerca') }}
                    <input
                        type="search"
                        name="search"
                        :value="filters.search"
                        class="filter-input"
                        :placeholder="t('products.search_placeholder', 'Nome prodotto')"
                    />
                </label>

                <label class="filter-field">
                    {{ t('admin.product_status', 'Stato') }}
                    <select name="status" class="filter-input">
                        <option value="all" :selected="filters.status === 'all'">{{ t('orders.all', 'Tutti') }}</option>
                        <option value="active" :selected="filters.status === 'active'">{{ t('admin.active_plural', 'Attivi') }}</option>
                        <option value="inactive" :selected="filters.status === 'inactive'">{{ t('admin.inactive_plural', 'Disattivati') }}</option>
                    </select>
                </label>

                <div class="sort-controls">
                    <label class="filter-field filter-field--sort">
                        {{ t('products.sort', 'Ordina') }}
                        <select name="sort" class="filter-input">
                            <option value="name" :selected="filters.sort === 'name'">{{ t('products.sort_name', 'Nome') }}</option>
                            <option value="price" :selected="filters.sort === 'price'">{{ t('products.sort_price', 'Prezzo') }}</option>
                            <option value="created_at" :selected="filters.sort === 'created_at'">{{ t('products.sort_created_at', 'Data inserimento') }}</option>
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
        </div>

        <section v-if="products.length === 0" class="empty-products">
            <p>{{ t('admin.no_products', 'Non ci sono prodotti.') }}</p>
        </section>

        <section v-else class="products-list">
            <article
                v-for="product in products"
                :key="product.id"
                class="product-card"
            >
                <header class="product-card-header">
                    <h2 class="product-title">{{ product.name }}</h2>

                    <span
                        v-if="product.description"
                        class="description-tooltip"
                        tabindex="0"
                        :aria-label="product.description"
                    >
                        <span class="description-info" aria-hidden="true"></span>
                        <span class="description-tooltip-content" role="tooltip">
                            {{ product.description }}
                        </span>
                    </span>
                </header>

                <div class="product-card-body">
                    <img
                        v-if="product.image_url"
                        :src="product.image_url"
                        :alt="product.name"
                        class="product-image"
                        loading="lazy"
                    />
                    <div v-else class="product-image product-image--placeholder">
                        {{ product.name.charAt(0) }}
                    </div>

                    <div class="product-card-info">
                        <p class="product-meta">
                            <strong>{{ product.price }} €</strong>
                            <span>/ {{ product.display_unit_type }}</span>
                        </p>

                        <span
                            class="status-pill"
                            :class="product.is_active ? 'status-pill--active' : 'status-pill--inactive'"
                            :title="product.is_active ? t('admin.active', 'Attivo') : t('admin.inactive', 'Disattivato')"
                            :aria-label="product.is_active ? t('admin.active', 'Attivo') : t('admin.inactive', 'Disattivato')"
                        >
                            <span class="status-dot" aria-hidden="true"></span>
                            {{ product.is_active ? 'On' : 'Off' }}
                        </span>

                        <div class="product-actions">
                            <a :href="route('admin.products.edit', product.id)" class="edit-link">
                                {{ t('admin.edit', 'Modifica') }}
                            </a>

                            <button
                                type="button"
                                class="delete-button"
                                @click="deleteProduct(product)"
                            >
                                {{ t('admin.delete', 'Elimina') }}
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    </PageContainer>
</template>

<style scoped>
.admin-products-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    margin-bottom: 16px;
}

.edit-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 28px;
    padding: 4px 7px;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    background: #fff7ed;
    color: #7c2d12;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
}

.delete-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 28px;
    padding: 4px 7px;
    border: 1px solid #fecaca;
    border-radius: 8px;
    background: #fff;
    color: #b91c1c;
    cursor: pointer;
    font: inherit;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.edit-link:hover {
    background: #ffedd5;
}

.delete-button:hover {
    background: #fee2e2;
}

.empty-products,
.product-card {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.filters-row {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 10px;
    margin-bottom: 16px;
}

.filters-form {
    display: flex;
    flex: 1 1 520px;
    flex-wrap: wrap;
    align-items: end;
    gap: 10px;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.create-product-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 148px;
    padding: 10px 14px;
    border-radius: 10px;
    background: #7c2d12;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}

.create-product-link:hover,
.create-product-link:focus-visible {
    background: #5f220d;
    outline: none;
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

.products-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(245px, 100%), 1fr));
    gap: 12px;
}

.product-card {
    display: grid;
    gap: 8px;
}

.product-card-header {
    position: relative;
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 8px;
}

.product-card-body {
    display: grid;
    grid-template-columns: 82px minmax(0, 1fr);
    gap: 10px;
    align-items: start;
}

.product-card-info {
    display: grid;
    align-content: start;
    gap: 8px;
    min-width: 0;
}

.product-image {
    width: 100%;
    aspect-ratio: 1;
    border-radius: 10px;
    object-fit: cover;
    background: #fff7ed;
}

.product-image--placeholder {
    display: grid;
    place-items: center;
    color: #7c2d12;
    font-size: 26px;
    font-weight: 800;
}

.product-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
}

.product-title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.2;
}

.description-tooltip {
    position: relative;
    flex: 0 0 auto;
}

.description-info {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    box-sizing: border-box;
    border: 1px solid #fed7aa;
    border-radius: 999px;
    color: #7c2d12;
    position: relative;
    cursor: help;
}

.description-info::before,
.description-info::after {
    position: absolute;
    left: 50%;
    border-radius: 999px;
    background: currentColor;
    content: '';
    transform: translateX(-50%);
}

.description-info::before {
    top: 7px;
    width: 2px;
    height: 6px;
}

.description-info::after {
    top: 4px;
    width: 2px;
    height: 2px;
}

.description-tooltip-content {
    position: absolute;
    z-index: 10;
    top: 24px;
    right: 0;
    width: min(220px, 70vw);
    padding: 8px 10px;
    border-radius: 10px;
    background: #111827;
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    line-height: 1.35;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-4px);
    transition: opacity 150ms ease, transform 150ms ease;
}

.description-tooltip:hover .description-tooltip-content,
.description-tooltip:focus .description-tooltip-content,
.description-tooltip:focus-within .description-tooltip-content {
    opacity: 1;
    transform: translateY(0);
}

.product-meta {
    margin: 0;
    color: #555;
    font-size: 14px;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    justify-self: start;
    padding: 3px 6px;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    text-transform: uppercase;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: currentColor;
}

.status-pill--active {
    background: #dcfce7;
    color: #15803d;
}

.status-pill--inactive {
    background: #fee2e2;
    color: #b91c1c;
}

@media (max-width: 640px) {
    .admin-products-header {
        gap: 8px;
        margin-bottom: 12px;
    }

    .filters-row {
        gap: 8px;
        margin-bottom: 12px;
    }

    .filters-form {
        gap: 8px;
        padding: 8px;
    }

    .filter-field {
        flex-basis: 140px;
    }

    .create-product-link {
        width: 100%;
        min-height: 38px;
    }

    .products-list {
        gap: 10px;
    }

    .empty-products,
    .product-card {
        padding: 10px;
    }

    .product-card {
        gap: 8px;
    }

    .product-title {
        font-size: 15px;
    }
}
</style>
