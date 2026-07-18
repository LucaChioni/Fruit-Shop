<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import flatpickr from 'flatpickr';
import { Italian } from 'flatpickr/dist/l10n/it.js';
import 'flatpickr/dist/flatpickr.css';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    order: Object,
    isAdminView: {
        type: Boolean,
        default: false,
    },
    orderStatuses: {
        type: Array,
        default: () => [],
    },
    pickupAtMin: {
        type: String,
        default: '',
    },
    pickupDateMax: {
        type: String,
        default: '',
    },
    closedPickupDates: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const t = useTranslations();
const pickupAtDefaultParts = props.order.pickup_at_input?.split('T') ?? ['', ''];
const pickupAtMinDate = props.pickupAtMin?.split('T')[0] ?? '';
const closedPickupDates = new Set(props.closedPickupDates ?? []);
const pickupDateError = ref('');
const dateInputLocale = computed(() => page.props.locale === 'it' ? 'it-IT' : 'en-US');
const pickupDateInput = ref(null);
const pickupTimeInput = ref(null);
let pickupDatePicker = null;
let pickupTimePicker = null;

const statusForm = useForm({
    status: props.order.status,
});

const pickupForm = useForm({
    pickup_date: pickupAtDefaultParts[0] ?? '',
    pickup_time: pickupAtDefaultParts[1] ?? '',
});

const cancelForm = useForm({});

function updateStatus() {
    statusForm.patch(route('admin.orders.status.update', props.order.id), {
        preserveScroll: true,
    });
}

function updatePickup() {
    validatePickupDate();

    if (pickupDateError.value) {
        return;
    }

    pickupForm.transform((data) => ({
        pickup_at: data.pickup_date && data.pickup_time ? `${data.pickup_date}T${data.pickup_time}` : '',
    })).patch(route('orders.pickup.update', props.order.id), {
        preserveScroll: true,
    });
}

function validatePickupDate() {
    pickupDateError.value = '';

    if (! pickupForm.pickup_date) {
        return;
    }

    if (closedPickupDates.has(pickupForm.pickup_date)) {
        pickupForm.pickup_date = '';
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
        defaultDate: pickupForm.pickup_date || null,
        disable: [...closedPickupDates],
        locale: page.props.locale === 'it' ? Italian : 'default',
        maxDate: props.pickupDateMax,
        minDate: pickupAtMinDate,
        onChange: (selectedDates, dateValue) => {
            pickupForm.pickup_date = dateValue;
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
        defaultDate: pickupForm.pickup_time || null,
        enableTime: true,
        noCalendar: true,
        time_24hr: true,
        onChange: (selectedDates, timeValue) => {
            pickupForm.pickup_time = timeValue;
        },
    });
}

function cancelOrder() {
    if (! confirm(t('orders.cancel_confirm', 'Vuoi annullare questo ordine?'))) {
        return;
    }

    cancelForm.patch(route('orders.cancel', props.order.id), {
        preserveScroll: true,
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
        <header class="order-header">
            <PageNav />

            <div v-if="page.props.flash?.success" class="flash-message flash-message--success">
                {{ page.props.flash.success }}
            </div>

            <div v-if="page.props.flash?.error" class="flash-message flash-message--error">
                {{ page.props.flash.error }}
            </div>
        </header>

        <section class="order-section">
            <p class="order-meta">
                <strong>{{ t('orders.reference', 'Riferimento') }}:</strong> {{ order.order_number ?? '#' + order.id }} · {{ order.created_at }} ·
                <span class="order-status" :class="`order-status--${order.status}`">
                    {{ t(`orders.${order.status}`, order.status) }}
                </span>
            </p>

            <p v-if="order.pickup_at" class="order-meta order-meta--pickup">
                <strong>{{ t('orders.pickup', 'Ritiro') }}:</strong> {{ order.pickup_at }}
            </p>

            <p v-if="page.props.shop?.address" class="order-meta">
                <strong>{{ t('shop.address', 'Indirizzo negozio') }}:</strong> {{ page.props.shop.address }}
                <a
                    v-if="page.props.shop.mapsUrl"
                    :href="page.props.shop.mapsUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="maps-link"
                >
                    {{ t('shop.maps_link', 'Apri in Google Maps') }}
                </a>
            </p>

            <p class="order-meta order-meta--client">
                <strong>{{ t('orders.customer', 'Cliente') }}:</strong> {{ order.customer_name }}
            </p>

            <p v-if="order.notes" class="order-meta">
                <strong>{{ t('orders.notes', 'Note') }}:</strong> {{ order.notes }}
            </p>
        </section>

        <section v-if="isAdminView" class="order-section">
            <h2>{{ t('orders.manage_status', 'Gestione stato') }}</h2>

            <form class="status-form" @submit.prevent="updateStatus">
                <label class="status-label">
                    {{ t('orders.order_status', 'Stato ordine') }}
                    <select v-model="statusForm.status" class="status-select">
                        <option
                            v-for="status in orderStatuses"
                            :key="status"
                            :value="status"
                        >
                            {{ t(`orders.${status}`, status) }}
                        </option>
                    </select>
                </label>

                <button
                    type="submit"
                    class="status-button"
                    :disabled="statusForm.processing"
                >
                    {{ t('orders.update_status', 'Aggiorna stato') }}
                </button>
            </form>

            <p v-if="statusForm.errors.status" class="status-error">
                {{ statusForm.errors.status }}
            </p>
        </section>

        <section v-if="!isAdminView && order.status === 'pending'" class="order-section">
            <h2>{{ t('orders.manage_order', 'Gestisci ordine') }}</h2>

            <form class="pickup-form" @submit.prevent="updatePickup">
                <div class="pickup-label">
                    <span>{{ t('orders.update_pickup', 'Modifica data e ora di ritiro') }}</span>
                    <div class="pickup-actions">
                        <div class="pickup-fields">
                            <input
                                ref="pickupDateInput"
                                v-model="pickupForm.pickup_date"
                                type="text"
                                class="pickup-input"
                                :lang="dateInputLocale"
                                :placeholder="page.props.locale === 'it' ? 'gg/mm/aaaa' : 'mm/dd/yyyy'"
                                :aria-label="t('checkout.pickup_date', 'Data di ritiro')"
                                required
                            />
                            <input
                                ref="pickupTimeInput"
                                v-model="pickupForm.pickup_time"
                                type="text"
                                class="pickup-input"
                                placeholder="HH:mm"
                                :aria-label="t('checkout.pickup_time', 'Ora di ritiro')"
                                required
                            />
                        </div>

                        <button type="submit" class="pickup-button" :disabled="pickupForm.processing">
                            {{ t('orders.save_pickup', 'Salva ritiro') }}
                        </button>
                    </div>
                    <span class="pickup-help">
                        {{ t('checkout.pickup_help', "Il ritiro non è possibile prima di 2 ore dall'ordine. Fasce: 11:00-13:00 e 16:00-19:30. Domenica e festivi esclusi.") }}
                    </span>
                </div>
            </form>

            <p v-if="pickupDateError" class="status-error">
                {{ pickupDateError }}
            </p>

            <p v-if="pickupForm.errors.pickup_at" class="status-error">
                {{ pickupForm.errors.pickup_at }}
            </p>

            <form class="cancel-form" @submit.prevent="cancelOrder">
                <button type="submit" class="cancel-button" :disabled="cancelForm.processing">
                    {{ t('orders.cancel_order', 'Annulla ordine') }}
                </button>
            </form>

            <p v-if="cancelForm.errors.order" class="status-error">
                {{ cancelForm.errors.order }}
            </p>
        </section>

        <section class="order-section">
            <h2>{{ t('orders.products', 'Prodotti') }}</h2>

            <div class="order-items">
                <article
                    v-for="item in order.items"
                    :key="item.id"
                    class="order-item"
                >
                    <div>
                        <h3 class="order-item-name">
                            {{ item.product_name }}
                        </h3>

                        <p class="order-item-details">
                            {{ item.quantity }} {{ item.unit_type }}
                            × {{ item.unit_price }} €
                        </p>
                    </div>

                    <strong>{{ item.line_total }} €</strong>
                </article>
            </div>
        </section>

        <footer class="order-total">
            <span>{{ t('cart.total', 'Totale') }}</span>
            <strong>{{ order.total_amount }} €</strong>
        </footer>
    </PageContainer>
</template>

<style scoped>
.order-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    margin-bottom: 16px;
}

.flash-message {
    flex: 1 1 100%;
}

.order-meta {
    margin: 0;
    color: #555;
}

.order-meta--pickup {
    margin-top: 6px;
    font-weight: 700;
}

.order-meta--client {
    margin-top: 6px;
    font-weight: 700;
}

.maps-link {
    margin-left: 6px;
    color: #166534;
    font-weight: 700;
}

:global(html.dark .maps-link) {
    color: #86efac;
}

.order-status {
    display: inline-flex;
    align-items: center;
    margin-left: 3px;
    padding: 2px 8px;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.3;
}

.order-status--pending {
    border-color: #fde68a;
    background: #fef3c7;
    color: #92400e;
}

.order-status--ready {
    border-color: #bbf7d0;
    background: #dcfce7;
    color: #166534;
}

.order-status--completed {
    border-color: #e5e7eb;
    background: #f3f4f6;
    color: #374151;
}

.order-status--cancelled {
    border-color: #fecaca;
    background: #fee2e2;
    color: #991b1b;
}

.order-section {
    margin-bottom: 16px;
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.order-section h2 {
    margin: 0 0 8px;
    font-size: 18px;
}

.order-items {
    display: grid;
    gap: 8px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: 0;
}

.order-item-name {
    margin: 0 0 3px;
    font-size: 17px;
}

.order-item-details {
    margin: 0;
    color: #555;
}

.order-total {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    padding: 14px;
    border-radius: 12px;
    background: #f3f4f6;
    font-size: 18px;
}

.products-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.products-link:hover {
    text-decoration: underline;
}

.flash-message {
    margin-top: 0;
    padding: 10px 12px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-weight: 600;
}

@media (max-width: 640px) {
    .order-header {
        gap: 8px;
        margin-bottom: 12px;
    }

    .order-section,
    .order-total {
        margin-bottom: 12px;
        padding: 10px;
    }
}

.flash-message--success {
    background: #dcfce7;
    border-color: #bbf7d0;
    color: #15803d;
}

.flash-message--error {
    border-color: #fecaca;
    background: #fee2e2;
    color: #b91c1c;
}

.status-form {
    display: flex;
    flex-wrap: wrap;
    align-items: end;
    gap: 12px;
}

.status-label {
    display: grid;
    gap: 6px;
    font-weight: 600;
}

.status-select {
    box-sizing: border-box;
    min-width: min(180px, 100%);
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
}

.status-button {
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    background: #7c2d12;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.status-button:hover {
    background: #5f220d;
}

.status-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.status-error {
    margin: 8px 0 0;
    color: #b91c1c;
    font-weight: 600;
}

.pickup-form {
    display: block;
}

.pickup-label {
    display: grid;
    gap: 6px;
    font-weight: 600;
}

.pickup-input {
    box-sizing: border-box;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    font: inherit;
}

.pickup-fields {
    display: grid;
    flex: 1;
    min-width: 0;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.pickup-actions {
    display: flex;
    align-items: end;
    gap: 12px;
}

.pickup-help {
    color: #666;
    font-size: 14px;
    font-weight: 400;
}

.pickup-button,
.cancel-button {
    padding: 9px 14px;
    border: 0;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}

.pickup-button {
    background: #166534;
}

:global(html.dark .pickup-fields input) {
    border-color: #334155;
    background: #0f172a;
    color: #e5e7eb;
}

.pickup-button:hover {
    background: #14532d;
}

.cancel-form {
    margin-top: 12px;
}

.cancel-button {
    background: #b91c1c;
}

.cancel-button:hover {
    background: #991b1b;
}

.pickup-button:disabled,
.cancel-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 520px) {
    .pickup-fields {
        grid-template-columns: 1fr;
    }
}
</style>
