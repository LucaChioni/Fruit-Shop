<script setup>
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

const t = useTranslations();
</script>

<template>
    <nav class="admin-nav" :aria-label="t('nav.admin', 'Admin')">
        <Link
            :href="route('admin.orders.index')"
            class="admin-nav-link"
            :class="{ 'admin-nav-link--active': route().current('admin.orders.*') }"
            :aria-current="route().current('admin.orders.*') ? 'page' : undefined"
        >
            {{ t('admin.manage_orders', 'Gestisci ordini') }}
        </Link>

        <Link
            :href="route('admin.products.index')"
            class="admin-nav-link"
            :class="{ 'admin-nav-link--active': route().current('admin.products.index') || route().current('admin.products.edit') }"
            :aria-current="(route().current('admin.products.index') || route().current('admin.products.edit')) ? 'page' : undefined"
        >
            {{ t('admin.manage_products', 'Gestisci prodotti') }}
        </Link>

        <Link
            :href="route('admin.products.create')"
            class="admin-nav-link"
            :class="{ 'admin-nav-link--active': route().current('admin.products.create') }"
            :aria-current="route().current('admin.products.create') ? 'page' : undefined"
        >
            {{ t('admin.new_product', 'Nuovo prodotto') }}
        </Link>
    </nav>
</template>

<style scoped>
.admin-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
    margin-bottom: 12px;
}

.admin-nav-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: min(144px, 100%);
    min-height: 36px;
    padding: 0 12px;
    border: 1px solid #7c2d12;
    border-radius: 999px;
    background: #fff;
    color: #7c2d12;
    font-weight: 700;
    font-size: 14px;
    text-align: center;
    text-decoration: none;
    transition: background 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
}

.admin-nav-link:hover,
.admin-nav-link:focus-visible {
    border-color: #9a3412;
    background: #fff7ed;
    outline: none;
    box-shadow: 0 0 0 3px rgba(124, 45, 18, 0.14);
}

.admin-nav-link--active {
    border-color: #7c2d12;
    background: #7c2d12;
    color: #fff;
    box-shadow: 0 8px 18px rgba(124, 45, 18, 0.18);
}

.admin-nav-link--active:hover,
.admin-nav-link--active:focus-visible {
    border-color: #5f220d;
    background: #5f220d;
}

@media (max-width: 640px) {
    .admin-nav {
        flex-wrap: nowrap;
        overflow-x: auto;
        margin-top: 6px;
        margin-bottom: 10px;
        scrollbar-width: none;
    }

    .admin-nav::-webkit-scrollbar {
        display: none;
    }

    .admin-nav-link {
        flex: 0 0 auto;
        min-width: auto;
        min-height: 34px;
        padding: 0 10px;
        font-size: 13px;
    }
}
</style>
