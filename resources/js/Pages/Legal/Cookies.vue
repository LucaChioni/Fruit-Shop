<script setup>
import PageContainer from '@/Components/PageContainer.vue';
import PageNav from '@/Components/PageNav.vue';
</script>

<template>
    <PageContainer>
        <header class="legal-header">
            <h1>Cookie policy</h1>
            <PageNav />
        </header>

        <section class="legal-card">
            <p>Il sito usa cookie tecnici necessari per login, sessione, sicurezza e carrello. Non sono configurati cookie pubblicitari o di profilazione.</p>

            <h2>Cookie tecnici</h2>
            <p>Servono a mantenere la sessione utente, ricordare il carrello ospite e proteggere le richieste tramite token CSRF.</p>

            <h2>Consenso</h2>
            <p>Per i soli cookie tecnici non è richiesto consenso preventivo. Se in futuro saranno aggiunti analytics o marketing, sarà necessario un banner di consenso dedicato.</p>

            <h2>Gestione</h2>
            <p>Puoi eliminare i cookie dal browser. Disabilitarli può impedire login, carrello e checkout.</p>
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
