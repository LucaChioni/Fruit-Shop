<script setup>
import ProductFormPage from '@/Components/ProductFormPage.vue';

defineProps({
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
    <ProductFormPage mode="create" :unit-types="unitTypes" :categories="categories" />
</template>
