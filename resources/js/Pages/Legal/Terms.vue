<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const t = useTranslations();
</script>

<template>
    <PageContainer narrow>
        <header class="legal-header">
            <PageNav />
        </header>

        <section class="legal-card">
            <p>{{ t('legal.terms.intro', 'Gli ordini effettuati sul sito sono richieste di acquisto con ritiro presso il negozio.') }}</p>

            <h2>{{ t('legal.terms.seller_title', 'Venditore') }}</h2>
            <p>{{ t('legal.terms.seller_text', 'Il venditore è Fruit Shop S.r.l., con sede in Via Placeholder 1, 00100 Roma (RM), contattabile a info@example.com.') }}</p>

            <h2>{{ t('legal.terms.acceptance_title', 'Accettazione dell’ordine') }}</h2>
            <p>{{ t('legal.terms.acceptance_text', 'L’ordine si considera confermato solo dopo accettazione da parte del negozio o comunicazione di disponibilità.') }}</p>

            <h2>{{ t('legal.terms.availability_title', 'Disponibilità prodotti') }}</h2>
            <p>{{ t('legal.terms.availability_text', 'I prodotti freschi possono variare in base a disponibilità e stagionalità. Il negozio può contattarti per eventuali sostituzioni.') }}</p>

            <h2>{{ t('legal.terms.fresh_limits_title', 'Limitazione disponibilità prodotti freschi') }}</h2>
            <p>{{ t('legal.terms.fresh_limits_text', 'La disponibilità dei prodotti freschi è limitata e può cambiare anche dopo l’invio dell’ordine. In caso di indisponibilità, il negozio può proporre alternative, modifiche o annullamento dell’ordine.') }}</p>

            <h2>{{ t('legal.terms.pickup_title', 'Ritiro') }}</h2>
            <p>{{ t('legal.terms.pickup_text', 'Il ritiro deve essere scelto negli orari disponibili indicati nel checkout. Gli ordini possono essere ritirati al massimo entro il giorno successivo.') }}</p>

            <h2>{{ t('legal.terms.prices_title', 'Prezzi e pagamenti') }}</h2>
            <p>{{ t('legal.terms.prices_text', 'I prezzi sono indicati per unità di misura. Eventuali arrotondamenti o variazioni di peso saranno comunicati al ritiro.') }}</p>

            <h2>{{ t('legal.terms.payment_title', 'Pagamento') }}</h2>
            <p>{{ t('legal.terms.payment_text', 'Il pagamento avviene al ritiro, salvo diversa indicazione del negozio. Il sito non gestisce pagamenti online.') }}</p>

            <h2>{{ t('legal.terms.changes_title', 'Modifica o cancellazione dell’ordine') }}</h2>
            <p>{{ t('legal.terms.changes_text', 'Per modificare o cancellare un ordine contatta il negozio prima dell’orario di ritiro. Le richieste tardive potrebbero non essere gestibili se l’ordine è già stato preparato.') }}</p>

            <h2>{{ t('legal.terms.no_pickup_title', 'Mancato ritiro') }}</h2>
            <p>{{ t('legal.terms.no_pickup_text', 'In caso di mancato ritiro entro l’orario concordato, il negozio può annullare l’ordine e rendere nuovamente disponibili i prodotti, salvo diverso accordo.') }}</p>

            <h2>{{ t('legal.terms.contacts_title', 'Contatti') }}</h2>
            <p>{{ t('legal.terms.contacts_text', "Per modifiche o cancellazioni contatta il negozio prima dell'orario di ritiro.") }}</p>
        </section>
    </PageContainer>
</template>

<style scoped>
.legal-header {
    margin-bottom: 16px;
}

.legal-card {
    display: grid;
    gap: 10px;
    padding: 14px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.legal-card h2,
.legal-card p {
    margin: 0;
}

.legal-card h2 {
    margin-top: 6px;
    font-size: 18px;
}

@media (max-width: 640px) {
    .legal-header {
        margin-bottom: 12px;
    }

    .legal-card {
        gap: 8px;
        padding: 10px;
    }
}
</style>
