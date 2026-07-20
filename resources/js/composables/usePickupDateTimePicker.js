import { usePage } from '@inertiajs/vue3';
import flatpickr from 'flatpickr';
import { Italian } from 'flatpickr/dist/l10n/it.js';
import 'flatpickr/dist/flatpickr.css';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';

export function usePickupDateTimePicker({
    form,
    pickupAtMin,
    pickupDateMax,
    closedPickupDates,
    closedDateError,
}) {
    const page = usePage();
    const closedDates = new Set(closedPickupDates ?? []);
    const pickupAtMinDate = pickupAtMin?.split('T')[0] ?? '';
    const pickupDateError = ref('');
    const dateInputLocale = computed(() => page.props.locale === 'it' ? 'it-IT' : 'en-US');
    const pickupDateInput = ref(null);
    const pickupTimeInput = ref(null);
    let pickupDatePicker = null;
    let pickupTimePicker = null;

    function validatePickupDate() {
        pickupDateError.value = '';

        if (! form.pickup_date) {
            return;
        }

        if (closedDates.has(form.pickup_date)) {
            form.pickup_date = '';
            pickupDateError.value = closedDateError();
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
            disable: [...closedDates],
            locale: page.props.locale === 'it' ? Italian : 'default',
            maxDate: pickupDateMax,
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

    return {
        dateInputLocale,
        pickupDateError,
        pickupDateInput,
        pickupTimeInput,
        validatePickupDate,
    };
}
