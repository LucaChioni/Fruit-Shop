<script setup>
import { router } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';

defineProps({
    products: Array,
    filters: Object,
});

function deleteProduct(product) {
    if (!confirm(`Eliminare ${product.name}?`)) {
        return;
    }

    router.delete(route('admin.products.destroy', product.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="admin-products-header">
            <h1 class="admin-products-title">Gestione prodotti</h1>

            <PageNav />

            <FlashMessage />

            <a :href="route('admin.products.create')" class="create-link">
                Nuovo prodotto
            </a>
        </header>

        <form :action="route('admin.products.index')" method="get" class="filters-form">
            <label class="filter-field">
                Cerca
                <input
                    type="search"
                    name="search"
                    :value="filters.search"
                    class="filter-input"
                    placeholder="Nome prodotto"
                />
            </label>

            <label class="filter-field">
                Stato
                <select name="status" class="filter-input">
                    <option value="all" :selected="filters.status === 'all'">Tutti</option>
                    <option value="active" :selected="filters.status === 'active'">Attivi</option>
                    <option value="inactive" :selected="filters.status === 'inactive'">Disattivati</option>
                </select>
            </label>

            <label class="filter-field">
                Ordina
                <select name="sort" class="filter-input">
                    <option value="name" :selected="filters.sort === 'name'">Nome</option>
                    <option value="newest" :selected="filters.sort === 'newest'">Più recenti</option>
                    <option value="price_asc" :selected="filters.sort === 'price_asc'">Prezzo crescente</option>
                    <option value="price_desc" :selected="filters.sort === 'price_desc'">Prezzo decrescente</option>
                </select>
            </label>

            <button type="submit" class="filter-button">Applica</button>
            <a :href="route('admin.products.index')" class="reset-link">Reset</a>
        </form>

        <section v-if="products.length === 0" class="empty-products">
            <p>Non ci sono prodotti.</p>
        </section>

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
                    <h2 class="product-title">{{ product.name }}</h2>
                    <p class="product-meta">
                        {{ product.price }} € / {{ product.unit_type }} ·
                        <span :class="product.is_active ? 'active' : 'inactive'">
                            {{ product.is_active ? 'Attivo' : 'Disattivato' }}
                        </span>
                    </p>
                    <p v-if="product.description" class="product-description">
                        {{ product.description }}
                    </p>
                </div>

                <div class="product-actions">
                    <a :href="route('admin.products.edit', product.id)" class="edit-link">
                        Modifica
                    </a>

                    <button
                        type="button"
                        class="delete-button"
                        @click="deleteProduct(product)"
                    >
                        Elimina
                    </button>
                </div>
            </article>
        </section>
    </PageContainer>
</template>

<style scoped>
.admin-products-header {
    margin-bottom: 24px;
}

.admin-products-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.create-link,
.edit-link {
    color: #7c2d12;
    font-weight: 600;
    text-decoration: none;
}

.delete-button {
    padding: 0;
    border: 0;
    background: transparent;
    color: #b91c1c;
    cursor: pointer;
    font: inherit;
    font-weight: 600;
}

.create-link:hover,
.edit-link:hover,
.delete-button:hover {
    text-decoration: underline;
}

.empty-products,
.product-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
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
    background: #7c2d12;
    color: white;
    cursor: pointer;
    font-weight: 600;
}

.reset-link {
    color: #7c2d12;
    font-weight: 600;
    text-decoration: none;
}

.products-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 14px;
}

.product-card {
    display: grid;
    gap: 12px;
}

.product-image {
    width: 100%;
    height: 120px;
    border-radius: 10px;
    object-fit: cover;
    background: #fff7ed;
}

.product-image--placeholder {
    display: grid;
    place-items: center;
    color: #7c2d12;
    font-size: 26px;
    font-weight: 800;
}

.product-actions {
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    justify-content: flex-start;
    gap: 12px;
}

.product-title {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 700;
}

.product-meta,
.product-description {
    margin: 0 0 6px;
    color: #555;
}

.active {
    color: #15803d;
    font-weight: 600;
}

.inactive {
    color: #b91c1c;
    font-weight: 600;
}
</style>
