<script setup>
const props = defineProps({
    direction: {
        type: String,
        required: true,
        validator: (value) => ['asc', 'desc'].includes(value),
    },
    ascendingLabel: {
        type: String,
        required: true,
    },
    descendingLabel: {
        type: String,
        required: true,
    },
    admin: {
        type: Boolean,
        default: false,
    },
});

function toggleDirection(event) {
    const form = event.currentTarget.form;
    const directionInput = form?.elements.namedItem('sort_direction');

    if (! form || ! directionInput) {
        return;
    }

    directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
    form.requestSubmit();
}
</script>

<template>
    <button
        type="button"
        class="sort-direction-button"
        :class="{ 'sort-direction-button--admin': admin }"
        :aria-label="direction === 'asc' ? ascendingLabel : descendingLabel"
        :title="direction === 'asc' ? ascendingLabel : descendingLabel"
        @click="toggleDirection"
    >
        <svg class="sort-direction-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <template v-if="direction === 'asc'">
                <path d="M12 19V5" />
                <path d="m6 11 6-6 6 6" />
            </template>
            <template v-else>
                <path d="M12 5v14" />
                <path d="m6 13 6 6 6-6" />
            </template>
        </svg>
    </button>
</template>

<style scoped>
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

.sort-direction-button--admin {
    color: #7c2d12;
}

.sort-direction-button:where(.sort-direction-button--admin):hover,
.sort-direction-button:where(.sort-direction-button--admin):focus-visible {
    border-color: #9a3412;
    background: #fff7ed;
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
</style>
