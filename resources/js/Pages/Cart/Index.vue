<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    items: Array,
    total: String,
});

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
    <main class="cart-page">
        <header class="cart-header">
            <h1 class="cart-title">Carrello</h1>

            <a :href="route('products.index')" class="products-link">
                Torna ai prodotti
            </a>

            <div v-if="$page.props.flash?.success" class="flash-message flash-message--success">
                {{ $page.props.flash.success }}
            </div>
        </header>

        <section v-if="items.length === 0" class="empty-cart">
            <p>Il carrello è vuoto.</p>
        </section>

        <form v-else class="cart-content" @submit.prevent="updateCart">
            <div class="cart-items">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="cart-item"
                >
                    <div>
                        <h2 class="cart-item-name">
                            {{ item.product_name }}
                        </h2>

                        <div class="quantity-form">
                            <label class="quantity-label">
                                Quantità
                                <input
                                    v-model="form.quantities[item.id]"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
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
                            Rimuovi
                        </button>
                    </div>
                </article>
            </div>

            <footer class="cart-summary">
                <span>Totale</span>
                <strong>{{ total }} €</strong>
            </footer>

            <button
                type="submit"
                class="update-cart-button"
                :disabled="form.processing"
            >
                Aggiorna carrello
            </button>
            <a :href="route('checkout.create')" class="checkout-link">
                Vai al checkout
            </a>
        </form>
    </main>
</template>

<style scoped>
.cart-page {
    padding: 40px;
}

.cart-header {
    margin-bottom: 24px;
}

.cart-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.products-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.products-link:hover {
    text-decoration: underline;
}

.empty-cart {
    padding: 24px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
    color: #555;
}

.cart-content {
    display: grid;
    gap: 24px;
}

.cart-items {
    display: grid;
    gap: 16px;
}

.cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
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

.flash-message {
    margin-top: 12px;
    font-weight: 600;
}

.flash-message--success {
    color: #15803d;
}

.cart-item-actions {
    display: grid;
    gap: 8px;
    justify-items: end;
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
    padding: 10px 16px;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    text-decoration: none;
}

.checkout-link:hover {
    background: #14532d;
}
</style>
