<script setup>
import ProductFormPage from '@/Components/ProductFormPage.vue';

defineProps({
    product: {
        type: Object,
        required: true,
    },
    unitTypes: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <ProductFormPage
        mode="edit"
        :product="product"
        :unit-types="unitTypes"
        :categories="categories"
    />
</template>
