<script setup>
import PageNav from '@/Components/PageNav.vue';
</script>

<template>
    <main class="admin-page">
        <header class="admin-header">
            <h1 class="admin-title">Admin</h1>

            <PageNav />
        </header>

        <section class="admin-card">
            <h2>Dashboard admin</h2>
            <p>Questa pagina è visibile solo agli utenti admin.</p>
        </section>
    </main>
</template>

<style scoped>
.admin-page {
    padding: 40px;
}

.admin-header {
    margin-bottom: 24px;
}

.admin-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.admin-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}
</style>