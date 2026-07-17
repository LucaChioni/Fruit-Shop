<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    products: Array,
    filters: Object,
});

const t = useTranslations();
const page = usePage();

const quantities = reactive(Object.fromEntries(
    props.products.map((product) => [product.id, 1])
));

const form = useForm({
    product_id: null,
    quantity: 1,
});

const deleteForm = useForm({});

function addToCart(product) {
    form.product_id = product.id;
    form.quantity = quantities[product.id] || product.quantity_step;
    form.clearErrors();

    form.post(route('cart.items.store'), {
        preserveScroll: true,
        onSuccess: () => {
            quantities[product.id] = 1;
        },
    });
}

function removeFromCart(product) {
    if (! product.cart_item_id) {
        return;
    }

    deleteForm.delete(route('cart.items.destroy', product.cart_item_id), {
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

function cartQuantityUnit(product) {
    const quantity = Number(product.cart_quantity);

    if (product.unit_type_key === 'vaschetta') {
        return quantity > 1
            ? t('units.vaschetta_cart_plural', product.unit_type)
            : t('units.vaschetta_cart', product.unit_type);
    }

    if (quantity > 1 && product.unit_type_key === 'pz') {
        return t(`units.${product.unit_type_key}_plural`, product.unit_type);
    }

    return product.unit_type;
}

function quantityError(product) {
    const quantity = Number(quantities[product.id]);

    if (product.quantity_step === 1 && ! Number.isInteger(quantity)) {
        return t('validation.quantity_integer', 'La quantità deve essere un numero intero.');
    }

    if (form.product_id === product.id && form.errors.quantity) {
        return form.errors.quantity;
    }

    return null;
}

function clearQuantityError(product) {
    if (form.product_id === product.id) {
        form.clearErrors('quantity');
    }
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
            </div>
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
                    alt=""
                    class="product-image"
                    loading="lazy"
                />
                <div v-else class="product-image product-image--placeholder" aria-hidden="true">
                    {{ product.name.charAt(0) }}
                </div>

                <header class="product-card-header">
                    <h2 class="product-name" :title="product.name">{{ product.name }}</h2>

                    <span
                        v-if="product.description"
                        class="description-tooltip"
                        tabindex="0"
                        :aria-label="product.description"
                    >
                        <span class="description-info" aria-hidden="true"></span>
                    </span>
                </header>

                <div class="product-card-body">
                    <div class="product-card-info">
                        <p
                            v-if="page.props.auth.user"
                            class="product-cart-quantity"
                            :class="{ 'product-cart-quantity--empty': !product.cart_quantity }"
                        >
                            <template v-if="product.cart_quantity">
                                {{ t('cart.label', 'Carrello') }}:
                                <strong>{{ formatQuantity(product.cart_quantity) }}</strong>
                                {{ cartQuantityUnit(product) }}

                                <button
                                    type="button"
                                    class="remove-from-cart-button"
                                    :aria-label="t('cart.remove_from_cart', 'Elimina il prodotto dal carrello')"
                                    :title="t('cart.remove_from_cart', 'Elimina il prodotto dal carrello')"
                                    :disabled="deleteForm.processing"
                                    @click="removeFromCart(product)"
                                >
                                    <svg class="remove-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="m6 6 12 12" />
                                        <path d="m18 6-12 12" />
                                    </svg>
                                </button>
                            </template>
                        </p>

                        <p class="product-price">
                            <strong>{{ product.price }} €</strong>
                            <span>/ {{ product.unit_type }}</span>
                        </p>

                        <div v-if="page.props.auth.user" class="product-actions">
                            <label class="quantity-label">
                                <input
                                    v-model="quantities[product.id]"
                                    type="number"
                                    :min="product.quantity_step"
                                    :step="product.quantity_step"
                                    :inputmode="product.quantity_step === 1 ? 'numeric' : 'decimal'"
                                    class="quantity-input"
                                    :aria-label="t('products.quantity', 'Quantità')"
                                    @input="clearQuantityError(product)"
                                />
                                <span v-if="quantityError(product)" class="quantity-error" role="alert">
                                    {{ quantityError(product) }}
                                </span>
                            </label>

                            <button
                                type="button"
                                class="add-to-cart-button"
                                :aria-label="t('products.add_to_cart', 'Aggiungi al carrello')"
                                :title="t('products.add_to_cart', 'Aggiungi al carrello')"
                                @click="addToCart(product)"
                                :disabled="form.processing"
                            >
                                <svg class="action-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <circle cx="9" cy="21" r="1.6" />
                                    <circle cx="18" cy="21" r="1.6" />
                                    <path d="M3 7h2l2.2 10.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 10H6" />
                                    <path class="action-icon-plus" d="M21.5 -0.5v7" />
                                    <path class="action-icon-plus" d="M18 3h7" />
                                </svg>
                            </button>
                        </div>

                        <Link v-else :href="route('login')" class="login-required-link">
                            {{ t('products.login_required', 'Accedi per ordinare') }}
                        </Link>
                    </div>
                </div>
                <span v-if="product.description" class="description-tooltip-content" role="tooltip">
                    {{ product.description }}
                </span>
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

.sort-direction-button {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    box-sizing: border-box;
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
    grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 220px));
    justify-content: center;
    gap: 12px;
}

.product-card {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.product-card::before {
    position: absolute;
    z-index: 1;
    inset: 0;
    background: linear-gradient(135deg, rgb(255 255 255 / 0.68), rgb(255 255 255 / 0.9));
    border-radius: inherit;
    content: '';
    transition: opacity 200ms ease;
}

.product-card-header {
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

.product-card-header > * {
    transition: opacity 200ms ease;
}

.product-card-body {
    position: relative;
    z-index: 2;
    display: flex;
    flex: 1;
    min-height: 0;
    transition: opacity 200ms ease;
}

.product-card-info {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: flex-start;
    gap: 6px;
    min-width: 0;
}

.product-image {
    position: absolute;
    z-index: 0;
    inset: 0;
    width: 100%;
    height: 100%;
    border-radius: inherit;
    object-fit: cover;
}

.product-image--placeholder {
    z-index: 2;
    display: grid;
    place-items: center;
    background: #fff;
    color: rgb(22 101 52 / 0.35);
    font-size: 72px;
    font-weight: 800;
}

.product-name {
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

.product-card:has(.description-tooltip:hover),
.product-card:has(.description-tooltip:focus) {
    z-index: 1;
}

.product-card:has(.description-tooltip:hover)::before,
.product-card:has(.description-tooltip:focus)::before {
    opacity: 0;
}

.product-card:has(.description-tooltip:hover) .product-card-header,
.product-card:has(.description-tooltip:focus) .product-card-header {
    border-bottom-color: transparent;
}

.product-card:has(.description-tooltip:hover) .product-card-header > *,
.product-card:has(.description-tooltip:focus) .product-card-header > *,
.product-card:has(.description-tooltip:hover) .product-card-body,
.product-card:has(.description-tooltip:focus) .product-card-body {
    opacity: 0;
}

.product-card:has(.description-tooltip:hover) .product-card-body,
.product-card:has(.description-tooltip:focus) .product-card-body {
    pointer-events: none;
}

.product-card:has(.description-tooltip:hover) .description-tooltip-content,
.product-card:has(.description-tooltip:focus) .description-tooltip-content {
    opacity: 1;
    transform: translateX(-50%);
}

.product-price {
    margin: 0;
    font-size: 18px;
    line-height: 1.25;
}

.product-cart-quantity {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    max-width: 100%;
    min-height: 26px;
    box-sizing: border-box;
    margin: 0;
    padding: 3px 6px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
}

.product-cart-quantity--empty {
    visibility: hidden;
}

.add-to-cart-button {
    display: inline-flex;
    flex: 0 0 28px;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.product-actions {
    display: flex;
    align-items: end;
    gap: 6px;
}

.login-required-link {
    display: inline-flex;
    justify-content: center;
    padding: 10px 12px;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    background: #f0fdf4;
    color: #166534;
    font-size: 16px;
    font-weight: 700;
    line-height: 1.2;
    text-align: center;
    text-decoration: none;
}

.login-required-link:hover,
.login-required-link:focus-visible {
    border-color: #22c55e;
    background: #dcfce7;
    outline: none;
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

.action-icon {
    width: 17px;
    height: 17px;
    fill: none;
    overflow: visible;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
    transform: translateX(-1px);
}

.remove-from-cart-button {
    display: inline-flex;
    flex: 0 0 20px;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    margin-left: auto;
    padding: 0;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: currentColor;
    cursor: pointer;
}

.remove-from-cart-button:hover,
.remove-from-cart-button:focus-visible {
    background: rgb(22 101 52 / 0.16);
    outline: none;
}

.remove-from-cart-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.remove-icon {
    width: 13px;
    height: 13px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-width: 2.5;
}

.action-icon-plus {
    stroke-width: 2.4;
}

.add-to-cart-button:hover {
    background: #14532d;
}

.add-to-cart-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 731px) {
    .products-list {
        grid-template-columns: repeat(auto-fit, minmax(clamp(130px, 25vw, 160px), 160px));
        justify-content: center;
        gap: 4px;
    }

    .product-card {
        justify-self: center;
        width: 100%;
        max-width: 160px;
        gap: 8px;
        padding: 6px;
    }

    .product-name {
        font-size: 18px;
    }

    .product-cart-quantity {
        font-size: 12px;
    }
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
        grid-template-columns: repeat(auto-fit, minmax(clamp(130px, 25vw, 160px), 160px));
        justify-content: center;
        gap: 4px;
    }

    .product-card {
        justify-self: center;
        width: 100%;
        max-width: 160px;
        gap: 8px;
        padding: 6px;
    }

    .product-name {
        font-size: 18px;
    }

}
</style>
