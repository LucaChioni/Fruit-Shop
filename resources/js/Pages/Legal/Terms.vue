<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
</script>

<template>
    <PageContainer narrow>
        <header class="legal-header">
            <h1>Il Giardino della Frutta</h1>
            <PageNav />
        </header>

        <section class="legal-card">
            <p>Gli ordini effettuati sul sito sono richieste di acquisto con ritiro presso il negozio.</p>

            <h2>Disponibilità prodotti</h2>
            <p>I prodotti freschi possono variare in base a disponibilità e stagionalità. Il negozio può contattarti per eventuali sostituzioni.</p>

            <h2>Ritiro</h2>
            <p>Il ritiro deve essere scelto negli orari disponibili indicati nel checkout. Gli ordini possono essere ritirati al massimo entro il giorno successivo.</p>

            <h2>Prezzi e pagamenti</h2>
            <p>I prezzi sono indicati per unità di misura. Eventuali arrotondamenti o variazioni di peso saranno comunicati al ritiro.</p>

            <h2>Contatti</h2>
            <p>Per modifiche o cancellazioni contatta il negozio prima dell'orario di ritiro.</p>
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
