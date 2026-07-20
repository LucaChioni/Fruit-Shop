<script setup>
const model = defineModel({
    type: [Number, String],
    required: true,
});

defineProps({
    min: {
        type: Number,
        required: true,
    },
    step: {
        type: Number,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['input', 'change']);
</script>

<template>
    <label class="quantity-label">
        <input
            v-model="model"
            type="number"
            :min="min"
            :step="step"
            :inputmode="step === 1 ? 'numeric' : 'decimal'"
            class="quantity-input"
            :aria-label="label"
            :disabled="disabled"
            @input="$emit('input', $event)"
            @change="$emit('change', $event)"
        />
        <span v-if="error" class="quantity-error" role="alert">
            {{ error }}
        </span>
    </label>
</template>

<style scoped>
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
    font-size: 16px;
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
</style>
