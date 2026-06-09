<script setup>
defineProps({
    products: Array,
});
</script>

<template>
    <div style="padding: 40px">
        <h1>Prodotti</h1>

        <div v-if="products.length === 0">
            Nessun prodotto disponibile.
        </div>

        <div
            v-for="product in products"
            :key="product.id"
            style="margin-bottom: 20px; border: 1px solid #ddd; padding: 16px; border-radius: 8px;"
        >
            <h2>{{ product.name }}</h2>

            <p v-if="product.description">
                {{ product.description }}
            </p>

            <p>
                <strong>{{ product.price }} €</strong>
                /
                {{ product.unit_type }}
            </p>
        </div>
    </div>
</template>