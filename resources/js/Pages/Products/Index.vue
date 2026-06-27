<script setup>
import { useForm } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';

const props = defineProps({
    products: Array,
});

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
            <h1 class="products-title">Prodotti</h1>

            <PageNav />

            <FlashMessage />
        </header>

        <p v-if="products.length === 0" class="empty-message">
            Nessun prodotto disponibile al momento. Torna a trovarci più tardi.
        </p>

        <section v-else class="products-list">
            <article
                v-for="product in products"
                :key="product.id"
                class="product-card"
            >
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
                        Quantità
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
                        Aggiungi al carrello
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

.products-list {
    display: grid;
    gap: 20px;
}

.product-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
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
    justify-items: end;
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
