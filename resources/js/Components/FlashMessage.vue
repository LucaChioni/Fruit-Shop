<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();

defineProps({
    embedded: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div
        v-if="page.props.flash?.success"
        class="flash-message flash-message--success"
        :class="{ 'flash-message--embedded': embedded }"
        role="status"
        aria-live="polite"
    >
        {{ page.props.flash.success }}
    </div>

    <div
        v-if="page.props.flash?.error"
        class="flash-message flash-message--error"
        :class="{ 'flash-message--embedded': embedded }"
        role="alert"
    >
        {{ page.props.flash.error }}
    </div>
</template>

<style scoped>
.flash-message {
    margin-top: 12px;
    padding: 10px 12px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-weight: 700;
}

.flash-message--success {
    background: #dcfce7;
    border-color: #bbf7d0;
    color: #166534;
}

.flash-message--error {
    background: #fee2e2;
    border-color: #fecaca;
    color: #991b1b;
}

.flash-message--embedded {
    flex: 1 1 100%;
    margin-top: 0;
    font-weight: 600;
}

.flash-message--success:where(.flash-message--embedded) {
    color: #15803d;
}

.flash-message--error:where(.flash-message--embedded) {
    color: #b91c1c;
}
</style>
