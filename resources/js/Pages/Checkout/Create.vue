<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { usePickupDateTimePicker } from '@/composables/usePickupDateTimePicker';
import { useTranslations } from '@/i18n';

const props = defineProps({
    pickupAtDefault: {
        type: String,
        required: true,
    },
    pickupAtMin: {
        type: String,
        required: true,
    },
    pickupDateMax: {
        type: String,
        required: true,
    },
    closedPickupDates: {
        type: Array,
        default: () => [],
    },
});

const t = useTranslations();
const page = usePage();
const pickupAtDefaultParts = props.pickupAtDefault?.split('T') ?? ['', ''];

const form = useForm({
    pickup_date: pickupAtDefaultParts[0] ?? '',
    pickup_time: pickupAtDefaultParts[1] ?? '',
    notes: '',
});

const {
    dateInputLocale,
    pickupDateError,
    pickupDateInput,
    pickupTimeInput,
    validatePickupDate,
} = usePickupDateTimePicker({
    form,
    pickupAtMin: props.pickupAtMin,
    pickupDateMax: props.pickupDateMax,
    closedPickupDates: props.closedPickupDates,
    closedDateError: () => t('checkout.closed_date_error', 'Il ritiro non è disponibile la domenica o nei giorni festivi.'),
});

function submitOrder() {
    validatePickupDate();

    if (pickupDateError.value) {
        return;
    }

    form.transform((data) => ({
        pickup_at: data.pickup_date && data.pickup_time ? `${data.pickup_date}T${data.pickup_time}` : '',
        notes: data.notes,
    })).post(route('checkout.store'));
}

</script>

<template>
    <PageContainer narrow>
        <header class="checkout-header page-header">
            <PageNav />
        </header>

        <form class="checkout-form" @submit.prevent="submitOrder">
            <div class="form-field">
                <span>{{ t('checkout.pickup', 'Data e ora di ritiro') }}</span>
                <div class="pickup-fields">
                    <input
                        ref="pickupDateInput"
                        v-model="form.pickup_date"
                        type="text"
                        class="form-input"
                        :lang="dateInputLocale"
                        :placeholder="page.props.locale === 'it' ? 'gg/mm/aaaa' : 'mm/dd/yyyy'"
                        :aria-label="t('checkout.pickup_date', 'Data di ritiro')"
                        required
                    />
                    <input
                        ref="pickupTimeInput"
                        v-model="form.pickup_time"
                        type="text"
                        class="form-input"
                        placeholder="HH:mm"
                        :aria-label="t('checkout.pickup_time', 'Ora di ritiro')"
                        required
                    />
                </div>
                <span class="form-help">
                    {{ t('checkout.pickup_help', "Il ritiro non è possibile prima di 2 ore dall'ordine. Fasce: 11:00-13:00 e 16:00-19:30. Domenica e festivi esclusi.") }}
                </span>
                <p v-if="pickupDateError" class="form-error">
                    {{ pickupDateError }}
                </p>
                <p v-if="form.errors.pickup_at" class="form-error">
                    {{ form.errors.pickup_at }}
                </p>
            </div>

            <div class="form-field">
                <span>{{ t('checkout.notes', 'Note') }}</span>
                <textarea
                    v-model="form.notes"
                    class="form-textarea"
                    rows="4"
                    :aria-label="t('checkout.notes', 'Note')"
                />
                <p v-if="form.errors.notes" class="form-error">
                    {{ form.errors.notes }}
                </p>
            </div>

            <button type="submit" class="submit-button" :disabled="form.processing">
                {{ t('checkout.submit', 'Conferma ordine') }}
            </button>
        </form>
    </PageContainer>
</template>

<style scoped>
.checkout-header {
    margin-bottom: 16px;
}

.checkout-form {
    display: grid;
    gap: 16px;
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

.pickup-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

@media (max-width: 520px) {
    .pickup-fields {
        grid-template-columns: 1fr;
    }
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

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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
