<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import QuantityInput from '@/Components/QuantityInput.vue';
import SortDirectionButton from '@/Components/SortDirectionButton.vue';
import { submitFilterForm } from '@/filterForm';
import { useTranslations } from '@/i18n';

const props = defineProps({
    products: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
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
    const quantity = quantities[product.id];

    if (product.quantity_step === 1 && ! Number.isInteger(Number(quantity))) {
        return;
    }

    form.product_id = product.id;
    form.quantity = quantity ?? product.quantity_step;
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

</script>

<template>
    <PageContainer>
        <header class="products-header page-list-header">
            <PageNav />

            <FlashMessage />
        </header>

        <form
            :action="route('products.index')"
            method="get"
            class="filters-form"
            @change="submitFilterForm"
            @submit.prevent="submitFilterForm"
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

                <SortDirectionButton
                    :direction="filters.sort_direction"
                    :ascending-label="t('products.sort_asc', 'Ascendente')"
                    :descending-label="t('products.sort_desc', 'Discendente')"
                />
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
                        :aria-describedby="`product-description-${product.id}`"
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
                            <QuantityInput
                                v-model="quantities[product.id]"
                                :min="product.quantity_step"
                                :step="product.quantity_step"
                                :label="t('products.quantity', 'Quantità')"
                                :error="quantityError(product)"
                                @input="clearQuantityError(product)"
                            />

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
                <span
                    v-if="product.description"
                    :id="`product-description-${product.id}`"
                    class="description-tooltip-content"
                    role="tooltip"
                >
                    {{ product.description }}
                </span>
            </article>
        </section>
    </PageContainer>
</template>

<style scoped src="../../../css/product-cards.css"></style>

<style scoped>
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
    align-items: center;
    justify-content: center;
    min-height: 60px;
    box-sizing: border-box;
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
    .product-cart-quantity {
        font-size: 12px;
    }
}

@media (max-width: 640px) {
    .filters-form {
        gap: 8px;
        margin-bottom: 12px;
        padding: 8px;
    }

    .filter-field {
        flex-basis: 140px;
    }

}
</style>
