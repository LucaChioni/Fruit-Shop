<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import flatpickr from 'flatpickr';
import { Italian } from 'flatpickr/dist/l10n/it.js';
import 'flatpickr/dist/flatpickr.css';
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    pickupAtDefault: String,
    pickupAtMin: String,
    pickupDateMax: String,
    closedPickupDates: Array,
});

const t = useTranslations();
const page = usePage();
const pickupAtDefaultParts = props.pickupAtDefault?.split('T') ?? ['', ''];
const pickupAtMinDate = props.pickupAtMin?.split('T')[0] ?? '';
const closedPickupDates = new Set(props.closedPickupDates ?? []);
const pickupDateError = ref('');
const dateInputLocale = computed(() => page.props.locale === 'it' ? 'it-IT' : 'en-US');
const pickupDateInput = ref(null);
const pickupTimeInput = ref(null);
let pickupDatePicker = null;
let pickupTimePicker = null;

const form = useForm({
    pickup_date: pickupAtDefaultParts[0] ?? '',
    pickup_time: pickupAtDefaultParts[1] ?? '',
    notes: '',
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

function validatePickupDate() {
    pickupDateError.value = '';

    if (! form.pickup_date) {
        return;
    }

    if (closedPickupDates.has(form.pickup_date)) {
        form.pickup_date = '';
        pickupDateError.value = t('checkout.closed_date_error', 'Il ritiro non è disponibile la domenica o nei giorni festivi.');
    }
}

function setupPickupDatePicker() {
    if (! pickupDateInput.value) {
        return;
    }

    pickupDatePicker?.destroy();
    pickupDatePicker = flatpickr(pickupDateInput.value, {
        allowInput: false,
        altInput: true,
        altFormat: page.props.locale === 'it' ? 'd/m/Y' : 'm/d/Y',
        dateFormat: 'Y-m-d',
        defaultDate: form.pickup_date || null,
        disable: [...closedPickupDates],
        locale: page.props.locale === 'it' ? Italian : 'default',
        maxDate: props.pickupDateMax,
        minDate: pickupAtMinDate,
        onChange: (selectedDates, dateValue) => {
            form.pickup_date = dateValue;
            validatePickupDate();
        },
    });
}

function setupPickupTimePicker() {
    if (! pickupTimeInput.value) {
        return;
    }

    pickupTimePicker?.destroy();
    pickupTimePicker = flatpickr(pickupTimeInput.value, {
        allowInput: false,
        dateFormat: 'H:i',
        defaultDate: form.pickup_time || null,
        enableTime: true,
        noCalendar: true,
        time_24hr: true,
        onChange: (selectedDates, timeValue) => {
            form.pickup_time = timeValue;
        },
    });
}

onMounted(() => {
    setupPickupDatePicker();
    setupPickupTimePicker();
});

onBeforeUnmount(() => {
    pickupDatePicker?.destroy();
    pickupTimePicker?.destroy();
});

watch(dateInputLocale, async () => {
    await nextTick();
    setupPickupDatePicker();
});
</script>

<template>
    <PageContainer narrow>
        <header class="checkout-header">
            <PageNav />
        </header>

        <form class="checkout-form" @submit.prevent="submitOrder">
            <label class="form-field">
                {{ t('checkout.pickup', 'Data e ora di ritiro') }}
                <div class="pickup-fields">
                    <input
                        ref="pickupDateInput"
                        v-model="form.pickup_date"
                        type="text"
                        class="form-input"
                        :lang="dateInputLocale"
                        :placeholder="page.props.locale === 'it' ? 'gg/mm/aaaa' : 'mm/dd/yyyy'"
                        required
                    />
                    <input
                        ref="pickupTimeInput"
                        v-model="form.pickup_time"
                        type="text"
                        class="form-input"
                        placeholder="HH:mm"
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
