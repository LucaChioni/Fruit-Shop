<script setup>
import { useForm } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    products: Array,
    filters: Object,
});

const t = useTranslations();

const quantities = Object.fromEntries(
    props.products.map((product) => [product.id, product.quantity_step])
);

const form = useForm({
    product_id: null,
    quantity: 1,
});

function addToCart(product) {
    form.product_id = product.id;
    form.quantity = quantities[product.id] || product.quantity_step;

    form.post(route('cart.items.store'), {
        preserveScroll: true,
    });
}

function formatQuantity(quantity) {
    const value = Number(quantity);

    if (Number.isNaN(value)) {
        return quantity;
    }

    return value.toLocaleString(undefined, {
        maximumFractionDigits: 2,
        minimumFractionDigits: 0,
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
        <header class="products-header">
            <PageNav />

            <FlashMessage />
        </header>

        <form
            :action="route('products.index')"
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
                {{ t('products.sort', 'Ordina') }}
                <select name="sort" class="filter-input">
                    <option value="name" :selected="filters.sort === 'name'">{{ t('products.sort_name', 'Nome') }}</option>
                    <option value="price" :selected="filters.sort === 'price'">{{ t('products.sort_price', 'Prezzo') }}</option>
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
        </form>

        <p v-if="products.length === 0" class="empty-message">
            {{ t('products.empty', 'Nessun prodotto disponibile al momento. Torna a trovarci più tardi.') }}
        </p>

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
                <div v-else class="product-image product-image--placeholder">
                    {{ product.name.charAt(0) }}
                </div>

                <div>
                    <h2 class="product-name">{{ product.name }}</h2>

                    <p v-if="product.description" class="product-description">
                        {{ product.description }}
                    </p>

                    <p class="product-price">
                        <strong>{{ product.price }} €</strong>
                        <span>/ {{ product.unit_type }}</span>
                    </p>

                    <p v-if="product.cart_quantity" class="product-cart-quantity">
                        {{ t('cart.in_cart', 'Nel carrello') }}:
                        <strong>{{ formatQuantity(product.cart_quantity) }}</strong>
                        {{ product.unit_type }}
                    </p>
                </div>

                <div class="product-actions">
                    <label class="quantity-label">
                        {{ t('products.quantity', 'Quantità') }}
                        <input
                            v-model="quantities[product.id]"
                            type="number"
                            :min="product.quantity_step"
                            :step="product.quantity_step"
                            class="quantity-input"
                        />
                    </label>

                    <button
                        type="button"
                        class="add-to-cart-button"
                        @click="addToCart(product)"
                        :disabled="form.processing"
                    >
                        {{ t('products.add_to_cart', 'Aggiungi al carrello') }}
                    </button>
                </div>
            </article>
        </section>
    </PageContainer>
</template>

<style scoped>
.products-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    margin-bottom: 16px;
}

.empty-message {
    color: #666;
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

.sort-direction-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 34px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    color: #166534;
    cursor: pointer;
}

.sort-direction-button:hover,
.sort-direction-button:focus-visible {
    border-color: #22c55e;
    background: #f0fdf4;
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
    grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr));
    gap: 12px;
}

.product-card {
    display: grid;
    gap: 10px;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.product-image {
    width: 100%;
    height: 108px;
    border-radius: 10px;
    object-fit: cover;
    background: #ecfdf5;
}

.product-image--placeholder {
    display: grid;
    place-items: center;
    color: #166534;
    font-size: 28px;
    font-weight: 800;
}

.product-name {
    margin: 0 0 5px;
    font-size: 18px;
    font-weight: 700;
}

.product-description {
    margin: 0 0 6px;
    font-size: 14px;
    color: #444;
}

.product-price {
    margin: 0;
}

.product-cart-quantity {
    display: inline-flex;
    gap: 4px;
    margin: 8px 0 0;
    padding: 4px 8px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 13px;
    font-weight: 700;
}

.add-to-cart-button {
    padding: 8px 12px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.product-actions {
    display: grid;
    gap: 8px;
    justify-items: start;
}

.quantity-label {
    display: grid;
    gap: 4px;
    font-size: 14px;
    color: #444;
    font-weight: 600;
}

.quantity-input {
    width: 110px;
    padding: 7px 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.add-to-cart-button:hover {
    background: #14532d;
}

.add-to-cart-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .products-header {
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

    .products-list {
        gap: 10px;
    }

    .product-card {
        gap: 8px;
        padding: 10px;
    }

    .product-image {
        height: 88px;
    }

    .product-name {
        font-size: 17px;
    }
}
</style>
