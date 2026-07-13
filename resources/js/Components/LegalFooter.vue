<script setup>
import { ref } from 'vue';
import LegalDocument from '@/Components/LegalDocument.vue';
import Modal from '@/Components/Modal.vue';
import { useTranslations } from '@/i18n';

const t = useTranslations();
const activeLegalDocument = ref(null);

const links = [
    {
        key: 'privacy',
        labelKey: 'nav.privacy',
        fallback: 'Privacy',
    },
    {
        key: 'cookies',
        labelKey: 'nav.cookies',
        fallback: 'Cookie',
    },
    {
        key: 'terms',
        labelKey: 'nav.terms',
        fallback: 'Condizioni',
    },
];

function legalTitle(type) {
    return links.find((item) => item.key === type)?.labelKey ?? 'nav.privacy';
}

function legalFallback(type) {
    return links.find((item) => item.key === type)?.fallback ?? 'Privacy';
}

const companyInfo = [
    {
        labelKey: 'legal.company_name',
        fallback: 'Ragione sociale',
        value: 'Fruit Shop S.r.l.',
    },
    {
        labelKey: 'legal.registered_office',
        fallback: 'Sede legale',
        value: 'Via Placeholder 1, 00100 Roma (RM)',
    },
    {
        labelKey: 'legal.vat_number',
        fallback: 'P. IVA',
        value: 'IT00000000000',
    },
    {
        labelKey: 'legal.tax_code',
        fallback: 'Codice fiscale',
        value: '00000000000',
    },
    {
        labelKey: 'legal.rea',
        fallback: 'REA',
        value: 'RM-0000000',
    },
    {
        labelKey: 'legal.share_capital',
        fallback: 'Capitale sociale',
        value: 'Euro 10.000 i.v.',
    },
    {
        labelKey: 'legal.email',
        fallback: 'Email',
        value: 'info@example.com',
    },
    {
        labelKey: 'legal.pec',
        fallback: 'PEC',
        value: 'fruitshop@pec.example.com',
    },
];
</script>

<template>
    <footer class="legal-footer" :aria-label="t('legal.footer_label', 'Informazioni legali')">
        <section class="legal-company" :aria-label="t('legal.company_label', 'Dati aziendali')">
            <span class="legal-footer-brand">Fruit Shop</span>

            <dl class="legal-company-list">
                <div
                    v-for="item in companyInfo"
                    :key="item.labelKey"
                    class="legal-company-item"
                >
                    <dt>{{ t(item.labelKey, item.fallback) }}</dt>
                    <dd>{{ item.value }}</dd>
                </div>
            </dl>
        </section>

        <nav class="legal-footer-links">
            <button
                v-for="item in links"
                :key="item.key"
                type="button"
                class="legal-footer-link"
                @click="activeLegalDocument = item.key"
            >
                {{ t(item.labelKey, item.fallback) }}
            </button>
        </nav>

        <Modal :show="activeLegalDocument !== null" max-width="2xl" @close="activeLegalDocument = null">
            <section v-if="activeLegalDocument" class="legal-modal">
                <header class="legal-modal-header">
                    <h2 class="legal-modal-title">
                        {{ t(legalTitle(activeLegalDocument), legalFallback(activeLegalDocument)) }}
                    </h2>

                    <button
                        type="button"
                        class="legal-modal-close"
                        :aria-label="t('cookies.close', 'Chiudi avviso cookie')"
                        @click="activeLegalDocument = null"
                    >
                        ×
                    </button>
                </header>

                <div class="legal-modal-body">
                    <LegalDocument :type="activeLegalDocument" />
                </div>
            </section>
        </Modal>
    </footer>
</template>

<style scoped>
.legal-footer {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    width: min(1120px, 100%);
    gap: 20px;
    margin: 40px auto 0;
    padding-top: 20px;
    color: #6b7280;
    font-size: 14px;
}

.legal-footer::before {
    position: absolute;
    top: 0;
    left: 50%;
    width: 95vw;
    border-top: 1px solid #e5e7eb;
    content: '';
    transform: translateX(-50%);
}

.legal-footer-brand {
    display: block;
    margin-bottom: 10px;
    color: #374151;
    font-weight: 700;
}

.legal-company {
    max-width: 720px;
}

.legal-company-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    margin: 0;
}

.legal-company-item {
    display: inline-flex;
    gap: 4px;
}

.legal-company-item dt,
.legal-company-item dd {
    margin: 0;
}

.legal-company-item dt {
    color: #4b5563;
    font-weight: 700;
}

.legal-footer-links {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
}

.legal-footer-link {
    padding: 0;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
    font: inherit;
    text-decoration: none;
}

.legal-footer-link:hover {
    color: #166534;
    text-decoration: underline;
}

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
