<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
import { useTranslations } from '@/i18n';

const t = useTranslations();
</script>

<template>
    <PageContainer narrow>
        <header class="legal-header">
            <h1>Il Giardino della Frutta</h1>
            <PageNav />
        </header>

        <section class="legal-card">
            <p>
                {{ t('legal.privacy.intro', 'Questa informativa descrive come Fruit Shop tratta i dati personali necessari per gestire account, carrelli e ordini con ritiro in negozio.') }}
            </p>

            <h2>{{ t('legal.privacy.data_title', 'Dati trattati') }}</h2>
            <p>{{ t('legal.privacy.data_text', 'Nome, email, dati account, contenuto degli ordini, note inserite nel checkout e dati tecnici necessari al funzionamento del sito.') }}</p>

            <h2>{{ t('legal.privacy.controller_title', 'Titolare del trattamento') }}</h2>
            <p>{{ t('legal.privacy.controller_text', 'Il titolare del trattamento è Fruit Shop S.r.l., con sede in Via Placeholder 1, 00100 Roma (RM), contattabile a info@example.com.') }}</p>

            <h2>{{ t('legal.privacy.purpose_title', 'Finalità') }}</h2>
            <p>{{ t('legal.privacy.purpose_text', "I dati sono usati per creare e gestire ordini, comunicazioni operative, sicurezza dell'applicazione e obblighi amministrativi.") }}</p>

            <h2>{{ t('legal.privacy.legal_basis_title', 'Base giuridica') }}</h2>
            <p>{{ t('legal.privacy.legal_basis_text', "La base principale è l'esecuzione del servizio richiesto dall'utente. Alcuni dati possono essere conservati per obblighi di legge o legittimo interesse alla sicurezza.") }}</p>

            <h2>{{ t('legal.privacy.retention_title', 'Conservazione e diritti') }}</h2>
            <p>{{ t('legal.privacy.retention_text', 'Puoi chiedere accesso, rettifica, cancellazione, limitazione o portabilità dei dati scrivendo al titolare del negozio. Gli ordini possono essere conservati per obblighi contabili.') }}</p>

            <h2>{{ t('legal.privacy.account_retention_title', 'Tempi di conservazione') }}</h2>
            <p>{{ t('legal.privacy.account_retention_text', 'I dati dell’account sono conservati fino alla cancellazione dell’account. I dati relativi agli ordini sono conservati per il tempo necessario alla gestione dell’ordine e, ove richiesto, per gli obblighi amministrativi e contabili applicabili.') }}</p>

            <h2>{{ t('legal.privacy.complaint_title', 'Reclamo') }}</h2>
            <p>{{ t('legal.privacy.complaint_text', 'Hai inoltre diritto di proporre reclamo al Garante per la protezione dei dati personali.') }}</p>

            <h2>{{ t('legal.privacy.third_parties_title', 'Terze parti') }}</h2>
            <p>{{ t('legal.privacy.third_parties_text', 'In sviluppo le email possono essere intercettate da Mailpit. In produzione possono essere usati provider tecnici per hosting, email e manutenzione.') }}</p>
        </section>
    </PageContainer>
</template>

<style scoped>
.legal-header {
    margin-bottom: 24px;
}

.legal-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.legal-card {
    display: grid;
    gap: 12px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.legal-card h2,
.legal-card p {
    margin: 0;
}

.legal-card h2 {
    margin-top: 8px;
    font-size: 20px;
}
</style>
