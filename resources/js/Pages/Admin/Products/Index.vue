<script setup>
import { usePage } from '@inertiajs/vue3';
import PageNav from '@/Components/PageNav.vue';

defineProps({
    products: Array,
});

const page = usePage();
</script>

<template>
    <main class="admin-products-page">
        <header class="admin-products-header">
            <h1 class="admin-products-title">Gestione prodotti</h1>

            <PageNav />

            <div v-if="page.props.flash?.success" class="flash-message flash-message--success">
                {{ page.props.flash.success }}
            </div>

            <a :href="route('admin.products.create')" class="create-link">
                Nuovo prodotto
            </a>
        </header>

        <section v-if="products.length === 0" class="empty-products">
            <p>Non ci sono prodotti.</p>
        </section>

        <section v-else class="products-list">
            <article
                v-for="product in products"
                :key="product.id"
                class="product-card"
            >
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

                <a :href="route('admin.products.edit', product.id)" class="edit-link">
                    Modifica
                </a>
            </article>
        </section>
    </main>
</template>

<style scoped>
.admin-products-page {
    padding: 40px;
}

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

.create-link:hover,
.edit-link:hover {
    text-decoration: underline;
}

.flash-message {
    margin-bottom: 12px;
    font-weight: 600;
}

.flash-message--success {
    color: #15803d;
}

.empty-products,
.product-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.products-list {
    display: grid;
    gap: 16px;
}

.product-card {
    display: flex;
    justify-content: space-between;
    gap: 24px;
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
