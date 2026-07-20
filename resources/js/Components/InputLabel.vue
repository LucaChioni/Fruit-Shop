<script setup>
defineProps({
    value: {
        type: String,
    },
    as: {
        type: String,
        default: 'label',
        validator: (value) => ['label', 'span'].includes(value),
    },
});
</script>

<template>
    <component :is="as" class="block text-sm font-medium text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </component>
</template>
