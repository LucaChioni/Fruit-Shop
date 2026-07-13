<script setup>
import { useTranslations } from '@/i18n';

defineProps({
    type: {
        type: String,
        required: true,
    },
});

const t = useTranslations();
</script>

<template>
    <div class="legal-document">
        <template v-if="type === 'privacy'">
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
        </template>

        <template v-else-if="type === 'cookies'">
            <p>{{ t('legal.cookies.intro', 'Il sito usa cookie tecnici necessari per login, sessione, sicurezza e carrello. Non sono configurati cookie pubblicitari o di profilazione.') }}</p>

            <h2>{{ t('legal.cookies.technical_title', 'Cookie tecnici') }}</h2>
            <p>{{ t('legal.cookies.technical_text', 'Servono a mantenere la sessione utente, ricordare il carrello ospite e proteggere le richieste tramite token CSRF.') }}</p>

            <h2>{{ t('legal.cookies.consent_title', 'Consenso') }}</h2>
            <p>{{ t('legal.cookies.consent_text', 'Per i soli cookie tecnici non è richiesto consenso preventivo. Se in futuro saranno aggiunti analytics o marketing, sarà necessario un banner di consenso dedicato.') }}</p>

            <h2>{{ t('legal.cookies.management_title', 'Gestione') }}</h2>
            <p>{{ t('legal.cookies.management_text', 'Puoi eliminare i cookie dal browser. Disabilitarli può impedire login, carrello e checkout.') }}</p>
        </template>

        <template v-else-if="type === 'terms'">
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
</style>
