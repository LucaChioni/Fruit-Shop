<script setup>
import { useForm } from '@inertiajs/vue3';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    customerName: String,
    pickupAtDefault: String,
    pickupAtMin: String,
});

const t = useTranslations();

const form = useForm({
    customer_name: props.customerName ?? '',
    pickup_at: props.pickupAtDefault ?? '',
    notes: '',
});

function submitOrder() {
    form.post(route('checkout.store'));
}
</script>

<template>
    <PageContainer>
        <h1 class="checkout-title">{{ t('checkout.title', 'Checkout') }}</h1>

        <PageNav />

        <form class="checkout-form" @submit.prevent="submitOrder">
            <label class="form-field">
                {{ t('checkout.name', 'Nome') }}
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
                {{ t('checkout.pickup', 'Data e ora di ritiro') }}
                <input
                    v-model="form.pickup_at"
                    type="datetime-local"
                    class="form-input"
                    :min="pickupAtMin"
                    required
                />
                <span class="form-help">
                    {{ t('checkout.pickup_help', "Il ritiro non è possibile prima di 2 ore dall'ordine. Fasce: 11:00-13:00 e 16:00-19:30.") }}
                </span>
                <p v-if="form.errors.pickup_at" class="form-error">
                    {{ form.errors.pickup_at }}
                </p>
            </label>

            <label class="form-field">
                {{ t('checkout.notes', 'Note') }}
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
                {{ t('checkout.submit', 'Conferma ordine') }}
            </button>
        </form>
    </PageContainer>
</template>

<style scoped>
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

.form-help {
    color: #666;
    font-size: 14px;
    font-weight: 400;
}
</style>
