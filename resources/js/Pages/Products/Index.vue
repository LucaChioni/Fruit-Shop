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
                    <option value="price_asc" :selected="filters.sort === 'price_asc'">{{ t('products.sort_price_asc', 'Prezzo crescente') }}</option>
                    <option value="price_desc" :selected="filters.sort === 'price_desc'">{{ t('products.sort_price_desc', 'Prezzo decrescente') }}</option>
                </select>
            </label>
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
