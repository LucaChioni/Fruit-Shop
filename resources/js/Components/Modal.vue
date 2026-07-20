<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

let bodyLockCount = 0;
let previousBodyOverflow = '';

function lockBodyScroll() {
    if (bodyLockCount === 0) {
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
    }

    bodyLockCount += 1;
}

function unlockBodyScroll() {
    bodyLockCount = Math.max(0, bodyLockCount - 1);

    if (bodyLockCount === 0) {
        document.body.style.overflow = previousBodyOverflow;
    }
}

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    labelledBy: {
        type: String,
        default: undefined,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);
let closeTimer = null;
let ownsBodyLock = false;

function updateVisibility(show) {
    if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }

    if (show) {
        if (! ownsBodyLock) {
            lockBodyScroll();
            ownsBodyLock = true;
        }

        showSlot.value = true;
        nextTick(() => {
            if (dialog.value && ! dialog.value.open) {
                dialog.value.showModal();
            }
        });

        return;
    }

    if (ownsBodyLock) {
        unlockBodyScroll();
        ownsBodyLock = false;
    }

    closeTimer = setTimeout(() => {
        dialog.value?.close();
        showSlot.value = false;
        closeTimer = null;
    }, 200);
}

watch(
    () => props.show,
    updateVisibility,
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    updateVisibility(props.show);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    if (closeTimer) {
        clearTimeout(closeTimer);
    }

    if (ownsBodyLock) {
        unlockBodyScroll();
    }
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <dialog
        class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent"
        ref="dialog"
        :aria-labelledby="labelledBy"
        @cancel.prevent="close"
    >
        <div
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            scroll-region
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div
                        class="modal-backdrop absolute inset-0"
                    />
                </div>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 transform overflow-hidden rounded-lg bg-white shadow-xl transition-all sm:mx-auto sm:w-full"
                    :class="maxWidthClass"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>
