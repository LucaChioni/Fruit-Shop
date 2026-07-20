<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/i18n';

defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['privacy', 'cookies', 'terms'].includes(value),
    },
});

const t = useTranslations();
const page = usePage();
const legalReplacements = computed(() => ({
    company: page.props.shop?.legal?.companyName ?? 'Fruit Shop S.r.l.',
    office: page.props.shop?.legal?.registeredOffice ?? 'Via Placeholder 1, 00100 Roma (RM)',
    email: page.props.shop?.legal?.email ?? 'info@example.com',
}));
</script>

<template>
    <div class="legal-document">
        <template v-if="type === 'privacy'">
            <p>
                {{ t('legal.privacy.intro') }}
            </p>

            <h2>{{ t('legal.privacy.data_title') }}</h2>
            <p>{{ t('legal.privacy.data_text') }}</p>

            <h2>{{ t('legal.privacy.controller_title') }}</h2>
            <p>{{ t('legal.privacy.controller_text', undefined, legalReplacements) }}</p>

            <h2>{{ t('legal.privacy.purpose_title') }}</h2>
            <p>{{ t('legal.privacy.purpose_text') }}</p>

            <h2>{{ t('legal.privacy.legal_basis_title') }}</h2>
            <p>{{ t('legal.privacy.legal_basis_text') }}</p>

            <h2>{{ t('legal.privacy.retention_title') }}</h2>
            <p>{{ t('legal.privacy.retention_text') }}</p>

            <h2>{{ t('legal.privacy.account_retention_title') }}</h2>
            <p>{{ t('legal.privacy.account_retention_text') }}</p>

            <h2>{{ t('legal.privacy.complaint_title') }}</h2>
            <p>{{ t('legal.privacy.complaint_text') }}</p>

            <h2>{{ t('legal.privacy.third_parties_title') }}</h2>
            <p>{{ t('legal.privacy.third_parties_text') }}</p>
        </template>

        <template v-else-if="type === 'cookies'">
            <p>{{ t('legal.cookies.intro') }}</p>

            <h2>{{ t('legal.cookies.technical_title') }}</h2>
            <p>{{ t('legal.cookies.technical_text') }}</p>

            <h2>{{ t('legal.cookies.consent_title') }}</h2>
            <p>{{ t('legal.cookies.consent_text') }}</p>

            <h2>{{ t('legal.cookies.management_title') }}</h2>
            <p>{{ t('legal.cookies.management_text') }}</p>
        </template>

        <template v-else-if="type === 'terms'">
            <p>{{ t('legal.terms.intro') }}</p>

            <h2>{{ t('legal.terms.seller_title') }}</h2>
            <p>{{ t('legal.terms.seller_text', undefined, legalReplacements) }}</p>

            <h2>{{ t('legal.terms.acceptance_title') }}</h2>
            <p>{{ t('legal.terms.acceptance_text') }}</p>

            <h2>{{ t('legal.terms.availability_title') }}</h2>
            <p>{{ t('legal.terms.availability_text') }}</p>

            <h2>{{ t('legal.terms.fresh_limits_title') }}</h2>
            <p>{{ t('legal.terms.fresh_limits_text') }}</p>

            <h2>{{ t('legal.terms.pickup_title') }}</h2>
            <p>{{ t('legal.terms.pickup_text') }}</p>
            <p v-if="page.props.shop?.address" class="shop-address">
                <strong>{{ t('shop.address', 'Indirizzo negozio') }}:</strong>
                {{ page.props.shop.address }}
                <a
                    v-if="page.props.shop.mapsUrl"
                    :href="page.props.shop.mapsUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ t('shop.maps_link', 'Apri in Google Maps') }}
                </a>
            </p>

            <h2>{{ t('legal.terms.prices_title') }}</h2>
            <p>{{ t('legal.terms.prices_text') }}</p>

            <h2>{{ t('legal.terms.payment_title') }}</h2>
            <p>{{ t('legal.terms.payment_text') }}</p>

            <h2>{{ t('legal.terms.changes_title') }}</h2>
            <p>{{ t('legal.terms.changes_text') }}</p>

            <h2>{{ t('legal.terms.no_pickup_title') }}</h2>
            <p>{{ t('legal.terms.no_pickup_text') }}</p>

            <h2>{{ t('legal.terms.contacts_title') }}</h2>
            <p>{{ t('legal.terms.contacts_text') }}</p>
        </template>
    </div>
</template>

<style scoped>
.legal-document {
    display: grid;
    gap: 10px;
}

.legal-document h2,
.legal-document p {
    margin: 0;
}

.legal-document h2 {
    margin-top: 6px;
    font-size: 18px;
}

.shop-address a {
    margin-left: 6px;
    color: #166534;
    font-weight: 700;
}

:global(html.dark .shop-address a) {
    color: #86efac;
}
</style>
