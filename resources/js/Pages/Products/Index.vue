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
            <h1 class="products-title">{{ t('products.title', 'Prodotti') }}</h1>

            <PageNav />

            <FlashMessage />
        </header>

        <form :action="route('products.index')" method="get" class="filters-form">
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

            <button type="submit" class="filter-button">{{ t('products.apply', 'Applica') }}</button>
            <a :href="route('products.index')" class="reset-link">{{ t('products.reset', 'Reset') }}</a>
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
    margin-bottom: 24px;
}

.products-title {
    margin-bottom: 8px;
    font-size: 28px;
    font-weight: 700;
}

.empty-message {
    color: #666;
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

.filter-button {
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.reset-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.products-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 16px;
}

.product-card {
    display: grid;
    gap: 14px;
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.product-image {
    width: 100%;
    height: 130px;
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
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 700;
}

.product-description {
    margin: 0 0 8px;
    color: #444;
}

.product-price {
    margin: 0;
}

.add-to-cart-button {
    padding: 10px 16px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.product-actions {
    display: grid;
    gap: 10px;
    justify-items: start;
}

.quantity-label {
    display: grid;
    gap: 6px;
    color: #444;
    font-weight: 600;
}

.quantity-input {
    width: 110px;
    padding: 8px;
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
</style>
