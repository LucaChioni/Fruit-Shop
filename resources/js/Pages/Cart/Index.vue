<script setup>
import { useForm } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    items: Array,
    total: String,
});

const t = useTranslations();

const form = useForm({
    quantities: Object.fromEntries(
        props.items.map((item) => [item.id, item.quantity])
    ),
});

function updateCart() {
    form.patch(route('cart.update'), {
        preserveScroll: true,
    });
}

const deleteForm = useForm({});

function removeItem(item) {
    deleteForm.delete(route('cart.items.destroy', item.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="cart-header">
            <h1 class="cart-title">Il Giardino della Frutta</h1>

            <PageNav />

            <FlashMessage />
        </header>

        <section v-if="items.length === 0" class="empty-cart">
            <h2>{{ t('cart.empty_title', 'Il carrello è vuoto') }}</h2>
            <p>{{ t('cart.empty_text', 'Aggiungi frutta e verdura fresca prima di procedere al checkout.') }}</p>
            <a :href="route('products.index')" class="products-link">
                {{ t('cart.go_products', 'Vai ai prodotti') }}
            </a>
        </section>

        <form v-else class="cart-content" @submit.prevent="updateCart">
            <div class="cart-items">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="cart-item"
                >
                    <img
                        v-if="item.product_image_url"
                        :src="item.product_image_url"
                        :alt="item.product_name"
                        class="cart-item-image"
                        loading="lazy"
                    />
                    <div v-else class="cart-item-image cart-item-image--placeholder">
                        {{ item.product_name.charAt(0) }}
                    </div>

                    <div>
                        <h2 class="cart-item-name">
                            {{ item.product_name }}
                        </h2>

                        <div class="quantity-form">
                            <label class="quantity-label">
                                {{ t('cart.quantity', 'Quantità') }}
                                <input
                                    v-model="form.quantities[item.id]"
                                    type="number"
                                    :min="item.quantity_step"
                                    :step="item.quantity_step"
                                    class="quantity-input"
                                />
                            </label>

                            <span class="cart-item-details">
                                {{ item.unit_type }} × {{ item.unit_price }} €
                            </span>
                        </div>
                    </div>

                    <div class="cart-item-actions">
                        <div class="cart-item-total">
                            {{ item.line_total }} €
                        </div>

                        <button
                            type="button"
                            class="remove-item-button"
                            :disabled="deleteForm.processing"
                            @click="removeItem(item)"
                        >
                            {{ t('cart.remove', 'Rimuovi') }}
                        </button>
                    </div>
                </article>
            </div>

            <footer class="cart-summary">
                <span>{{ t('cart.total', 'Totale') }}</span>
                <strong>{{ total }} €</strong>
            </footer>

            <button
                type="submit"
                class="update-cart-button"
                :disabled="form.processing"
            >
                {{ t('cart.update', 'Aggiorna carrello') }}
            </button>
            <a :href="route('checkout.create')" class="checkout-link">
                {{ t('cart.checkout', 'Procedi al checkout') }}
            </a>
        </form>
    </PageContainer>
</template>

<style scoped>
.cart-header {
    margin-bottom: 24px;
}

.cart-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.empty-cart {
    padding: 24px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
    color: #555;
}

.empty-cart h2 {
    margin: 0 0 8px;
    color: #111827;
    font-size: 20px;
}

.empty-cart p {
    margin: 0 0 12px;
}

.cart-content {
    display: grid;
    gap: 24px;
}

.cart-items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(260px, 100%), 1fr));
    gap: 16px;
}

.cart-item {
    display: grid;
    gap: 14px;
    padding: 16px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.cart-item-image {
    width: 100%;
    height: 130px;
    border-radius: 10px;
    object-fit: cover;
    background: #ecfdf5;
}

.cart-item-image--placeholder {
    display: grid;
    place-items: center;
    color: #166534;
    font-size: 24px;
    font-weight: 800;
}

.cart-item-name {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 700;
}

.cart-item-details {
    margin: 0;
    color: #555;
}

.cart-item-total {
    font-weight: 700;
}

.cart-summary {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    border-radius: 12px;
    background: #f3f4f6;
    font-size: 20px;
}

.quantity-form {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 8px;
}

.quantity-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.quantity-input {
    width: 90px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.update-cart-button {
    justify-self: start;
    padding: 10px 16px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.update-cart-button:hover {
    background: #14532d;
}

.update-cart-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.cart-item-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.remove-item-button {
    padding: 8px 12px;
    border: 1px solid #b91c1c;
    border-radius: 8px;
    background: white;
    color: #b91c1c;
    font-weight: 600;
    cursor: pointer;
}

.remove-item-button:hover {
    background: #fee2e2;
}

.remove-item-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.checkout-link {
    justify-self: start;
    display: inline-block;
    padding: 14px 22px;
    border-radius: 999px;
    background: #166534;
    color: white;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 20px rgb(22 101 52 / 0.18);
}

.checkout-link:hover {
    background: #14532d;
}

.products-link {
    color: #166534;
    font-weight: 700;
    text-decoration: none;
}

.products-link:hover {
    text-decoration: underline;
}
</style>
