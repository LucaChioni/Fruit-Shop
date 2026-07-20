<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import LegalModal from '@/Components/LegalModal.vue';
import { useTranslations } from '@/i18n';

const t = useTranslations();
const page = usePage();
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

const companyInfo = computed(() => [
    {
        labelKey: 'legal.company_name',
        fallback: 'Ragione sociale',
        value: page.props.shop?.legal?.companyName ?? 'Fruit Shop S.r.l.',
    },
    {
        labelKey: 'legal.registered_office',
        fallback: 'Sede legale',
        value: page.props.shop?.legal?.registeredOffice ?? 'Via Placeholder 1, 00100 Roma (RM)',
    },
    {
        labelKey: 'legal.vat_number',
        fallback: 'P. IVA',
        value: page.props.shop?.legal?.vatNumber ?? 'IT00000000000',
    },
    {
        labelKey: 'legal.tax_code',
        fallback: 'Codice fiscale',
        value: page.props.shop?.legal?.taxCode ?? '00000000000',
    },
    {
        labelKey: 'legal.rea',
        fallback: 'REA',
        value: page.props.shop?.legal?.rea ?? 'RM-0000000',
    },
    {
        labelKey: 'legal.share_capital',
        fallback: 'Capitale sociale',
        value: page.props.shop?.legal?.shareCapital ?? 'Euro 10.000 i.v.',
    },
    {
        labelKey: 'legal.email',
        fallback: 'Email',
        value: page.props.shop?.legal?.email ?? 'info@example.com',
    },
    {
        labelKey: 'legal.pec',
        fallback: 'PEC',
        value: page.props.shop?.legal?.pec ?? 'fruitshop@pec.example.com',
    },
]);
</script>

<template>
    <footer class="legal-footer" :aria-label="t('legal.footer_label', 'Informazioni legali')">
        <section class="legal-company" :aria-label="t('legal.company_label', 'Dati aziendali')">
            <span class="legal-footer-brand">{{ page.props.shop?.legal?.brand ?? 'Fruit Shop' }}</span>

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

        <LegalModal
            :show="activeLegalDocument !== null"
            :type="activeLegalDocument"
            :title="t(legalTitle(activeLegalDocument), legalFallback(activeLegalDocument))"
            @close="activeLegalDocument = null"
        />
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

</style>
