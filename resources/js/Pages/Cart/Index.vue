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

function cartQuantityUnit(item) {
    const quantity = Number(item.quantity);

    if (item.unit_type_key === 'vaschetta') {
        return quantity > 1
            ? t('units.vaschetta_cart_plural', item.unit_type)
            : t('units.vaschetta_cart', item.unit_type);
    }

    if (quantity > 1 && item.unit_type_key === 'pz') {
        return t('units.pz_plural', item.unit_type);
    }

    return item.unit_type;
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
            <footer class="cart-summary">
                <div>
                    <span>{{ t('cart.total', 'Totale') }}</span>
                    <strong>{{ total }} €</strong>
                </div>

                <a :href="route('checkout.create')" class="checkout-link">
                    {{ t('cart.checkout', "Procedi all'ordine") }}
                </a>
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
                        >
                            <span class="description-info" aria-hidden="true"></span>
                        </span>
                    </header>

                    <div class="cart-item-body">
                        <div class="cart-item-info">
                            <div class="cart-item-total">
                                {{ formatDisplayNumber(item.quantity) }} {{ cartQuantityUnit(item) }} × {{ formatDisplayNumber(item.unit_price) }} € = {{ formatDisplayNumber(item.line_total) }} €
                            </div>

                            <p class="cart-item-details">
                                <strong>{{ formatDisplayNumber(item.unit_price) }} €</strong>
                                <span>/ {{ item.unit_type }}</span>
                            </p>

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
                    <span v-if="item.product_description" class="description-tooltip-content" role="tooltip">
                        {{ item.product_description }}
                    </span>
                </article>
            </div>
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
    grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 220px));
    justify-content: center;
    gap: 12px;
}

.cart-item {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #ecfdf5;
}

.cart-item::before {
    position: absolute;
    z-index: 1;
    inset: 0;
    background: linear-gradient(135deg, rgb(255 255 255 / 0.68), rgb(255 255 255 / 0.9));
    border-radius: inherit;
    content: '';
    transition: opacity 200ms ease;
}

.cart-item-header {
    position: relative;
    z-index: 3;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgb(22 101 52 / 0.25);
    transition: border-color 200ms ease;
}

.cart-item-header > * {
    transition: opacity 200ms ease;
}

.cart-item-body {
    position: relative;
    z-index: 2;
    display: flex;
    flex: 1;
    min-height: 0;
    transition: opacity 200ms ease;
}

.cart-item-info {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: flex-start;
    gap: 6px;
    min-width: 0;
}

.cart-item-image {
    position: absolute;
    z-index: 0;
    inset: 0;
    width: 100%;
    height: 100%;
    border-radius: inherit;
    object-fit: cover;
}

.cart-item-image--placeholder {
    z-index: 2;
    display: grid;
    place-items: center;
    background: #fff;
    color: rgb(22 101 52 / 0.35);
    font-size: 72px;
    font-weight: 800;
}

.cart-item-name {
    flex: 1;
    min-width: 0;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 20px;
    font-weight: 700;
    line-height: 1.2;
}

.description-tooltip {
    position: relative;
    flex: 0 0 auto;
    transform: translateY(4px);
}

.description-info {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    box-sizing: border-box;
    border: 1px solid currentColor;
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
    top: calc(100% + 8px);
    left: 50%;
    box-sizing: border-box;
    width: 100%;
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
    transform: translate(-50%, -4px);
    transition: opacity 150ms ease, transform 150ms ease;
}

.cart-item:has(.description-tooltip:hover),
.cart-item:has(.description-tooltip:focus) {
    z-index: 1;
}

.cart-item:has(.description-tooltip:hover)::before,
.cart-item:has(.description-tooltip:focus)::before {
    opacity: 0;
}

.cart-item:has(.description-tooltip:hover) .cart-item-header,
.cart-item:has(.description-tooltip:focus) .cart-item-header {
    border-bottom-color: transparent;
}

.cart-item:has(.description-tooltip:hover) .cart-item-header > *,
.cart-item:has(.description-tooltip:focus) .cart-item-header > *,
.cart-item:has(.description-tooltip:hover) .cart-item-body,
.cart-item:has(.description-tooltip:focus) .cart-item-body {
    opacity: 0;
}

.cart-item:has(.description-tooltip:hover) .cart-item-body,
.cart-item:has(.description-tooltip:focus) .cart-item-body {
    pointer-events: none;
}

.cart-item:has(.description-tooltip:hover) .description-tooltip-content,
.cart-item:has(.description-tooltip:focus) .description-tooltip-content {
    opacity: 1;
    transform: translateX(-50%);
}

.cart-item-details {
    margin: 0;
    font-size: 18px;
    line-height: 1.25;
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

.quantity-label {
    display: flex;
    flex: 1 1 auto;
    min-width: 0;
    position: relative;
}

.quantity-input {
    width: 100%;
    height: 28px;
    box-sizing: border-box;
    padding: 4px 7px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 12px;
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

.products-link {
    color: #166534;
    font-weight: 700;
    text-decoration: none;
}

.products-link:hover {
    text-decoration: underline;
}

@media (max-width: 731px) {
    .cart-items {
        grid-template-columns: repeat(auto-fit, minmax(clamp(130px, 25vw, 170px), 170px));
        justify-content: center;
        gap: 10px;
    }

    .cart-item {
        justify-self: center;
        width: 100%;
        max-width: 170px;
        gap: 8px;
        padding: 10px;
    }

    .cart-item-name {
        font-size: 18px;
    }
}

@media (max-width: 640px) {
    .cart-header {
        gap: 8px;
        margin-bottom: 12px;
    }

    .cart-items {
        grid-template-columns: repeat(auto-fit, minmax(clamp(130px, 25vw, 170px), 170px));
        justify-content: center;
        gap: 10px;
    }

    .cart-item {
        justify-self: center;
        width: 100%;
        max-width: 170px;
        gap: 8px;
        padding: 10px;
    }

    .cart-item-name {
        font-size: 18px;
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
