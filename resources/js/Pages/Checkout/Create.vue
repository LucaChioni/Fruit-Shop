<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    customerName: String,
});

const form = useForm({
    customer_name: props.customerName ?? '',
    notes: '',
});

function submitOrder() {
    form.post(route('checkout.store'));
}
</script>

<template>
    <main class="checkout-page">
        <h1 class="checkout-title">Checkout</h1>

        <form class="checkout-form" @submit.prevent="submitOrder">
            <label class="form-field">
                Nome
                <input
                    v-model="form.customer_name"
                    type="text"
                    class="form-input"
                    required
                />
                <p v-if="form.errors.customer_name" class="form-error">
                    {{ form.errors.customer_name }}
                </p>
            </label>

            <label class="form-field">
                Note
                <textarea
                    v-model="form.notes"
                    class="form-textarea"
                    rows="4"
                />
                <p v-if="form.errors.notes" class="form-error">
                    {{ form.errors.notes }}
                </p>
            </label>

            <button type="submit" class="submit-button">
                Conferma ordine
            </button>
        </form>
    </main>
</template>

<style scoped>
.checkout-page {
    padding: 40px;
}

.checkout-title {
    margin: 0 0 24px;
    font-size: 28px;
    font-weight: 700;
}

.checkout-form {
    display: grid;
    gap: 16px;
    max-width: 480px;
}

.form-field {
    display: grid;
    gap: 8px;
    font-weight: 600;
}

.form-input,
.form-textarea {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font: inherit;
}

.submit-button {
    justify-self: start;
    padding: 10px 16px;
    border: 0;
    border-radius: 8px;
    background: #166534;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.submit-button:hover {
    background: #14532d;
}

.form-error {
    margin: 0;
    color: #b91c1c;
    font-size: 14px;
    font-weight: 500;
}
</style>