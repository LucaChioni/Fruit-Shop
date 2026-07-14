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
        props.items.map((item) => [item.id, formatDisplayNumber(item.quantity)])
    ),
});

function updateCart() {
    form.patch(route('cart.update'), {
        preserveScroll: true,
    });
}

const deleteForm = useForm({});

function quantityError(item) {
    const quantity = Number(form.quantities[item.id]);

    if (item.quantity_step === 1 && ! Number.isInteger(quantity)) {
        return t('validation.quantity_integer', 'La quantità deve essere un numero intero.');
    }

    return form.errors[`quantities.${item.id}`] ?? null;
}

function clearQuantityError(item) {
    form.clearErrors(`quantities.${item.id}`);
}

function formatDisplayNumber(value) {
    return String(value).replace(/([,.])0+$/, '');
}

function removeItem(item) {
    deleteForm.delete(route('cart.items.destroy', item.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="cart-header">
            <PageNav />

            <FlashMessage />
        </header>

        <section v-if="items.length === 0" class="empty-cart">
            <h2>{{ t('cart.empty_title', 'Il carrello è vuoto') }}</h2>
            <p>{{ t('cart.empty_text', 'Aggiungi frutta e verdura fresca prima di procedere al checkout.') }}</p>
        </section>

        <div v-else class="cart-content">
            <div class="cart-items">
                <article
                    v-for="item in items"
                    :key="item.id"
                    class="cart-item"
                >
                    <header class="cart-item-header">
                        <h2 class="cart-item-name">
                            {{ item.product_name }}
                        </h2>

                        <span
                            v-if="item.product_description"
                            class="description-tooltip"
                            tabindex="0"
                            :aria-label="item.product_description"
                        >
                            <span class="description-info" aria-hidden="true"></span>
                            <span class="description-tooltip-content" role="tooltip">
                                {{ item.product_description }}
                            </span>
                        </span>
                    </header>

                    <div class="cart-item-body">
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

                        <div class="cart-item-info">
                            <p class="cart-item-details">
                                {{ formatDisplayNumber(item.unit_price) }} € / {{ item.unit_type }}
                            </p>

                            <div class="cart-item-total">
                                {{ formatDisplayNumber(item.line_total) }} €
                            </div>

                            <div class="cart-item-actions">
                                <label class="quantity-label">
                                    <input
                                        v-model="form.quantities[item.id]"
                                        type="number"
                                        :min="item.quantity_step"
                                        :step="item.quantity_step"
                                        :inputmode="item.quantity_step === 1 ? 'numeric' : 'decimal'"
                                        class="quantity-input"
                                        :aria-label="t('cart.quantity', 'Quantità')"
                                        :disabled="form.processing"
                                        @input="clearQuantityError(item)"
                                        @change="updateCart"
                                    />
                                    <span v-if="quantityError(item)" class="quantity-error" role="alert">
                                        {{ quantityError(item) }}
                                    </span>
                                </label>

                                <button
                                    type="button"
                                    class="remove-item-button"
                                    :aria-label="t('cart.remove', 'Rimuovi')"
                                    :disabled="deleteForm.processing"
                                    @click="removeItem(item)"
                                >
                                    <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M3 6h18" />
                                        <path d="M8 6V4h8v2" />
                                        <path d="M19 6l-1 14H6L5 6" />
                                        <path d="M10 11v5" />
                                        <path d="M14 11v5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <footer class="cart-summary">
                <div>
                    <span>{{ t('cart.total', 'Totale') }}</span>
                    <strong>{{ total }} €</strong>
                </div>

                <a :href="route('checkout.create')" class="checkout-link">
                    {{ t('cart.checkout', 'Procedi al checkout') }}
                </a>
            </footer>
        </div>
    </PageContainer>
</template>

<style scoped>
.cart-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    margin-bottom: 16px;
}

.cart-header :deep(.flash-message) {
    flex: 1 1 100%;
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
    gap: 16px;
}

.cart-items {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(245px, 100%), 1fr));
    gap: 12px;
}

.cart-item {
    display: grid;
    gap: 8px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.cart-item-header {
    position: relative;
    display: flex;
    align-items: start;
    justify-content: space-between;
    gap: 8px;
}

.cart-item-body {
    display: grid;
    grid-template-columns: minmax(82px, 44%) minmax(0, 1fr);
    gap: 10px;
    align-items: start;
}

.cart-item-info {
    display: grid;
    align-content: start;
    gap: 8px;
    margin-top: 12px;
    min-width: 0;
}

.cart-item-image {
    width: 100%;
    aspect-ratio: 1;
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
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    color: #166534;
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
    border: 1px solid #111827;
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

.cart-item-details {
    margin: 0;
    color: #555;
    font-size: 14px;
}

.cart-item-total {
    font-size: 14px;
    font-weight: 700;
}

.cart-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px;
    border-radius: 12px;
    background: #f3f4f6;
    font-size: 20px;
}

.cart-summary div {
    display: flex;
    gap: 8px;
}

.quantity-label {
    display: block;
    flex: 1 1 auto;
    min-width: 0;
    position: relative;
}

.quantity-input {
    width: 100%;
    height: 30px;
    box-sizing: border-box;
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.quantity-error {
    position: absolute;
    z-index: 20;
    top: calc(100% + 6px);
    left: 0;
    display: block;
    width: max-content;
    max-width: 170px;
    padding: 6px 8px;
    border-radius: 8px;
    border: 1px solid #fecaca;
    background: #b91c1c;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
    box-shadow: 0 8px 18px rgb(185 28 28 / 0.24);
}

.quantity-error::before {
    position: absolute;
    top: -4px;
    left: 14px;
    width: 8px;
    height: 8px;
    background: inherit;
    content: '';
    transform: rotate(45deg);
}

.cart-item-actions {
    display: flex;
    align-items: end;
    gap: 6px;
    width: 100%;
}

.remove-item-button {
    display: inline-flex;
    flex: 0 0 32px;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 30px;
    border: 1px solid #b91c1c;
    border-radius: 8px;
    background: white;
    color: #b91c1c;
    font-weight: 600;
    cursor: pointer;
}

.action-icon {
    width: 17px;
    height: 17px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.remove-item-button:hover {
    background: #fee2e2;
}

.remove-item-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.checkout-link {
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

@media (max-width: 640px) {
    .cart-header {
        gap: 8px;
        margin-bottom: 12px;
    }

    .cart-items {
        gap: 10px;
    }

    .cart-item {
        padding: 10px;
    }

    .cart-summary {
        flex-direction: column;
        align-items: stretch;
    }

    .cart-summary div {
        justify-content: space-between;
    }

    .checkout-link {
        text-align: center;
    }
}
</style>
