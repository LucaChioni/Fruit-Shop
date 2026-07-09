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
            <p>{{ t('legal.cookies.intro', 'Il sito usa cookie tecnici necessari per login, sessione, sicurezza e carrello. Non sono configurati cookie pubblicitari o di profilazione.') }}</p>

            <h2>{{ t('legal.cookies.technical_title', 'Cookie tecnici') }}</h2>
            <p>{{ t('legal.cookies.technical_text', 'Servono a mantenere la sessione utente, ricordare il carrello ospite e proteggere le richieste tramite token CSRF.') }}</p>

            <h2>{{ t('legal.cookies.consent_title', 'Consenso') }}</h2>
            <p>{{ t('legal.cookies.consent_text', 'Per i soli cookie tecnici non è richiesto consenso preventivo. Se in futuro saranno aggiunti analytics o marketing, sarà necessario un banner di consenso dedicato.') }}</p>

            <h2>{{ t('legal.cookies.management_title', 'Gestione') }}</h2>
            <p>{{ t('legal.cookies.management_text', 'Puoi eliminare i cookie dal browser. Disabilitarli può impedire login, carrello e checkout.') }}</p>
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
