<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import QuantityInput from '@/Components/QuantityInput.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    total: {
        type: String,
        required: true,
    },
});

const t = useTranslations();

const form = useForm({
    quantities: Object.fromEntries(
        props.items.map((item) => [item.id, formatDisplayNumber(item.quantity)])
    ),
});

function updateCart(item) {
    if (item.quantity_step === 1 && ! Number.isInteger(Number(form.quantities[item.id]))) {
        return;
    }

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
        <header class="cart-header page-list-header">
            <PageNav />

            <FlashMessage />
        </header>

        <section v-if="items.length === 0" class="empty-cart">
            <h2>{{ t('cart.empty_title', 'Il carrello è vuoto') }}</h2>
            <p>{{ t('cart.empty_text', 'Aggiungi frutta e verdura fresca prima di procedere al checkout.') }}</p>
        </section>

        <div v-else class="cart-content">
            <footer class="cart-summary">
                <div>
                    <span>{{ t('cart.total', 'Totale') }}</span>
                    <strong>{{ total }} €</strong>
                </div>

                <Link :href="route('checkout.create')" class="checkout-link">
                    {{ t('cart.checkout', "Procedi all'ordine") }}
                </Link>
            </footer>

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
                    <div v-else class="cart-item-image cart-item-image--placeholder" aria-hidden="true">
                        {{ item.product_name.charAt(0) }}
                    </div>

                    <header class="cart-item-header">
                        <h2 class="cart-item-name">
                            {{ item.product_name }}
                        </h2>

                        <span
                            v-if="item.product_description"
                            class="description-tooltip"
                            tabindex="0"
                            :aria-label="item.product_description"
                            :aria-describedby="`cart-item-description-${item.id}`"
                        >
                            <span class="description-info" aria-hidden="true"></span>
                        </span>
                    </header>

                    <div class="cart-item-body">
                        <div class="cart-item-info">
                            <div class="cart-item-total">
                                {{ formatDisplayNumber(item.unit_price) }} € × {{ formatDisplayNumber(item.quantity) }} = {{ formatDisplayNumber(item.line_total) }} €
                            </div>

                            <p class="cart-item-details">
                                <strong>{{ formatDisplayNumber(item.unit_price) }} €</strong>
                                <span>/ {{ item.unit_type }}</span>
                            </p>

                            <div class="cart-item-actions">
                                <QuantityInput
                                    v-model="form.quantities[item.id]"
                                    :min="item.quantity_step"
                                    :step="item.quantity_step"
                                    :label="t('cart.quantity', 'Quantità')"
                                    :error="quantityError(item)"
                                    :disabled="form.processing"
                                    @input="clearQuantityError(item)"
                                    @change="updateCart(item)"
                                />

                                <button
                                    type="button"
                                    class="remove-item-button"
                                    :aria-label="t('cart.remove_from_cart', 'Elimina il prodotto dal carrello')"
                                    :title="t('cart.remove_from_cart', 'Elimina il prodotto dal carrello')"
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
                    <span
                        v-if="item.product_description"
                        :id="`cart-item-description-${item.id}`"
                        class="description-tooltip-content"
                        role="tooltip"
                    >
                        {{ item.product_description }}
                    </span>
                </article>
            </div>
        </div>
    </PageContainer>
</template>

<style scoped src="../../../css/product-cards.css"></style>

<style scoped>
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

.cart-item-total {
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
    min-height: 26px;
    box-sizing: border-box;
    padding: 3px 6px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
}

.cart-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 20px;
    border-radius: 12px;
    background: #f3f4f6;
    font-size: 20px;
}

.cart-summary div {
    display: flex;
    gap: 8px;
}

.cart-item-actions {
    display: flex;
    align-items: end;
    gap: 6px;
}

.remove-item-button {
    display: inline-flex;
    flex: 0 0 28px;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
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
    border-radius: 12px;
    background: #166534;
    color: white;
    font-size: 18px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 10px 20px rgb(22 101 52 / 0.18);
}

.checkout-link:hover {
    background: #14532d;
}

@media (max-width: 731px) {
    .cart-item-total {
        font-size: 12px;
    }
}

@media (max-width: 640px) {
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
