<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div v-if="page.props.flash?.success" class="flash-message flash-message--success">
        {{ page.props.flash.success }}
    </div>

    <div v-if="page.props.flash?.error" class="flash-message flash-message--error">
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
</style>
