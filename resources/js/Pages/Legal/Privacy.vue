<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
</script>

<template>
    <PageContainer>
        <header class="legal-header">
            <h1>Privacy e GDPR</h1>
            <PageNav />
        </header>

        <section class="legal-card">
            <p>
                Questa informativa descrive come Fruit Shop tratta i dati personali necessari per gestire account, carrelli e ordini con ritiro in negozio.
            </p>

            <h2>Dati trattati</h2>
            <p>Nome, email, dati account, contenuto degli ordini, note inserite nel checkout e dati tecnici necessari al funzionamento del sito.</p>

            <h2>Finalità</h2>
            <p>I dati sono usati per creare e gestire ordini, comunicazioni operative, sicurezza dell'applicazione e obblighi amministrativi.</p>

            <h2>Base giuridica</h2>
            <p>La base principale è l'esecuzione del servizio richiesto dall'utente. Alcuni dati possono essere conservati per obblighi di legge o legittimo interesse alla sicurezza.</p>

            <h2>Conservazione e diritti</h2>
            <p>Puoi chiedere accesso, rettifica, cancellazione, limitazione o portabilità dei dati scrivendo al titolare del negozio. Gli ordini possono essere conservati per obblighi contabili.</p>

            <h2>Terze parti</h2>
            <p>In sviluppo le email possono essere intercettate da Mailpit. In produzione possono essere usati provider tecnici per hosting, email e manutenzione.</p>
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
