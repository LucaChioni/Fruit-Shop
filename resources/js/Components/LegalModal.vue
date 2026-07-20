<script setup>
import { ref, useId, watch } from 'vue';
import LegalDocument from '@/Components/LegalDocument.vue';
import Modal from '@/Components/Modal.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: null,
        validator: (value) => value === null || ['privacy', 'cookies', 'terms'].includes(value),
    },
    title: {
        type: String,
        required: true,
    },
});

defineEmits(['close']);

const t = useTranslations();
const titleId = `legal-modal-title-${useId()}`;
const displayedType = ref(props.type);
const displayedTitle = ref(props.title);

watch(
    () => [props.type, props.title],
    ([type, title]) => {
        if (type) {
            displayedType.value = type;
            displayedTitle.value = title;
        }
    },
);
</script>

<template>
    <Modal :show="show" max-width="2xl" :labelled-by="titleId" @close="$emit('close')">
        <section class="legal-modal">
            <header class="legal-modal-header">
                <h2 :id="titleId" class="legal-modal-title">
                    {{ displayedTitle }}
                </h2>

                <button
                    type="button"
                    class="legal-modal-close"
                    :aria-label="t('legal.close', 'Chiudi documento legale')"
                    @click="$emit('close')"
                >
                    ×
                </button>
            </header>

            <div class="legal-modal-body">
                <LegalDocument v-if="displayedType" :type="displayedType" />
            </div>
        </section>
    </Modal>
</template>

<style scoped>
.legal-modal {
    max-height: min(760px, calc(100vh - 48px));
    overflow-y: auto;
    padding: 18px;
}

.legal-modal-header {
    position: sticky;
    top: -18px;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin: -18px -18px 14px;
    padding: 16px 18px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
}

.legal-modal-title {
    margin: 0;
    color: #111827;
    font-size: 20px;
    font-weight: 800;
}

.legal-modal-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: #4b5563;
    cursor: pointer;
    font: inherit;
    font-size: 24px;
    line-height: 1;
}

.legal-modal-close:hover,
.legal-modal-close:focus-visible {
    background: #f3f4f6;
    color: #111827;
    outline: none;
}
</style>
