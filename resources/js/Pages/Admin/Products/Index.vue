<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import SortDirectionButton from '@/Components/SortDirectionButton.vue';
import { submitFilterForm } from '@/filterForm';
import { useTranslations } from '@/i18n';

defineProps({
    products: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const t = useTranslations();
const deletingProductId = ref(null);

function deleteProduct(product) {
    if (!confirm(t('admin.delete_product_confirm', 'Eliminare :name?').replace(':name', product.name))) {
        return;
    }

    router.delete(route('admin.products.destroy', product.id), {
        preserveScroll: true,
        onStart: () => {
            deletingProductId.value = product.id;
        },
        onFinish: () => {
            deletingProductId.value = null;
        },
    });
}
</script>

<template>
    <PageContainer>
        <header class="admin-products-header page-list-header">
            <PageNav />

            <FlashMessage />
        </header>

        <div class="filters-row">
            <Link :href="route('admin.products.create')" class="create-product-link">
                {{ t('admin.new_product', 'Aggiungi prodotto') }}
            </Link>

            <form
                :action="route('admin.products.index')"
                method="get"
                class="filters-form"
                @change="submitFilterForm"
                @submit.prevent="submitFilterForm"
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

                <label class="filter-field">
                    {{ t('products.category', 'Categoria') }}
                    <select name="category" class="filter-input">
                        <option value="all" :selected="filters.category === 'all'">{{ t('products.all_categories', 'Tutte le categorie') }}</option>
                        <option value="fruit" :selected="filters.category === 'fruit'">{{ t('categories.fruit', 'Frutta') }}</option>
                        <option value="vegetable" :selected="filters.category === 'vegetable'">{{ t('categories.vegetable', 'Verdura') }}</option>
                        <option value="dried_fruit" :selected="filters.category === 'dried_fruit'">{{ t('categories.dried_fruit', 'Frutta secca') }}</option>
                        <option value="herbs" :selected="filters.category === 'herbs'">{{ t('categories.herbs', 'Erbe aromatiche') }}</option>
                        <option value="mushrooms" :selected="filters.category === 'mushrooms'">{{ t('categories.mushrooms', 'Funghi') }}</option>
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

                    <SortDirectionButton
                        :direction="filters.sort_direction"
                        :ascending-label="t('products.sort_asc', 'Ascendente')"
                        :descending-label="t('products.sort_desc', 'Discendente')"
                        admin
                    />
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
                <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="product-image"
                    loading="lazy"
                />
                <div v-else class="product-image product-image--placeholder" aria-hidden="true">
                    {{ product.name.charAt(0) }}
                </div>

                <header class="product-card-header">
                    <h2 class="product-title">{{ product.name }}</h2>

                    <span
                        v-if="product.description"
                        class="description-tooltip"
                        tabindex="0"
                        :aria-label="product.description"
                        :aria-describedby="`admin-product-description-${product.id}`"
                    >
                        <span class="description-info" aria-hidden="true"></span>
                    </span>
                </header>

                <div class="product-card-body">
                    <div class="product-card-info">
                        <span
                            class="status-pill"
                            :class="product.is_active ? 'status-pill--active' : 'status-pill--inactive'"
                            :title="product.is_active ? t('admin.active', 'Attivo') : t('admin.inactive', 'Disattivato')"
                            :aria-label="product.is_active ? t('admin.active', 'Attivo') : t('admin.inactive', 'Disattivato')"
                        >
                            <span class="status-dot" aria-hidden="true"></span>
                            {{ product.is_active ? t('admin.active', 'Attivo') : t('admin.inactive', 'Inattivo') }}
                        </span>

                        <p class="product-meta">
                            <strong>{{ product.price }} €</strong>
                            <span>/ {{ product.display_unit_type }}</span>
                        </p>

                        <div class="product-actions">
                            <Link :href="route('admin.products.edit', product.id)" class="edit-link">
                                {{ t('admin.edit', 'Modifica') }}
                            </Link>

                            <button
                                type="button"
                                class="delete-button"
                                :disabled="deletingProductId !== null"
                                @click="deleteProduct(product)"
                            >
                                {{ t('admin.delete', 'Elimina') }}
                            </button>
                        </div>
                    </div>
                </div>
                <span
                    v-if="product.description"
                    :id="`admin-product-description-${product.id}`"
                    class="description-tooltip-content"
                    role="tooltip"
                >
                    {{ product.description }}
                </span>
            </article>
        </section>
    </PageContainer>
</template>

<style scoped src="../../../../css/product-cards.css"></style>

<style scoped>
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

.delete-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.empty-products {
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

.product-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
    flex-wrap: wrap;
    gap: 4px;
    max-width: 100%;
    min-height: 26px;
    box-sizing: border-box;
    padding: 3px 6px;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
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

    .empty-products {
        padding: 10px;
    }
}
</style>
