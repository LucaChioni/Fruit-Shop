<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/i18n';

const page = usePage();
const t = useTranslations();
</script>
<template>
    <nav class="page-nav">
        <Link :href="route('products.index')" class="page-nav-link">
            <span class="nav-icon nav-icon--products" aria-hidden="true"></span>
            {{ t('nav.products', 'Prodotti') }}
        </Link>

        <Link :href="route('cart.index')" class="page-nav-link">
            <span class="nav-icon nav-icon--cart" aria-hidden="true"></span>
            {{ t('nav.cart', 'Carrello') }}
        </Link>

        <Link
            v-if="page.props.auth.user"
            :href="route('orders.index')"
            class="page-nav-link"
        >
            <span class="nav-icon nav-icon--orders" aria-hidden="true"></span>
            {{ t('nav.orders', 'I miei ordini') }}
        </Link>

        <Link
            v-if="page.props.auth.user?.is_admin"
            :href="route('admin.dashboard')"
            class="page-nav-link page-nav-link--admin"
        >
            <span class="nav-icon nav-icon--admin" aria-hidden="true"></span>
            {{ t('nav.admin', 'Admin') }}
        </Link>

        <Link
            v-if="!page.props.auth.user"
            :href="route('login')"
            class="page-nav-link page-nav-link--auth"
        >
            <span class="nav-icon nav-icon--login" aria-hidden="true"></span>
            {{ t('nav.login', 'Login') }}
        </Link>

        <Link
            v-if="!page.props.auth.user"
            :href="route('register')"
            class="page-nav-link page-nav-link--auth"
        >
            <span class="nav-icon nav-icon--register" aria-hidden="true"></span>
            {{ t('nav.register', 'Registrati') }}
        </Link>

        <Link
            v-if="page.props.auth.user"
            :href="route('logout')"
            method="post"
            as="button"
            class="page-nav-link page-nav-link--button"
        >
            <span class="nav-icon nav-icon--logout" aria-hidden="true"></span>
            {{ t('nav.logout', 'Logout') }}
        </Link>

        <span class="language-switcher" :aria-label="t('nav.language', 'Lingua')">
            <Link
                :href="route('language.update', 'it')"
                method="post"
                as="button"
                class="language-button"
                :class="{ 'language-button--active': page.props.locale === 'it' }"
            >
                IT
            </Link>
            <Link
                :href="route('language.update', 'en')"
                method="post"
                as="button"
                class="language-button"
                :class="{ 'language-button--active': page.props.locale === 'en' }"
            >
                EN
            </Link>
        </span>
    </nav>
</template>

<style scoped>
.page-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 12px;
}

.page-nav-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #166534;
    font-weight: 600;
    text-decoration: none;
}

.nav-icon {
    position: relative;
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid currentColor;
    border-radius: 4px;
}

.nav-icon--products::after,
.nav-icon--orders::after,
.nav-icon--register::after {
    position: absolute;
    inset: 3px;
    border-top: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    content: '';
}

.nav-icon--cart {
    border-radius: 3px 3px 6px 6px;
}

.nav-icon--cart::before {
    position: absolute;
    top: -6px;
    left: 3px;
    width: 8px;
    height: 6px;
    border: 2px solid currentColor;
    border-bottom: 0;
    border-radius: 8px 8px 0 0;
    content: '';
}

.nav-icon--admin,
.nav-icon--login,
.nav-icon--logout {
    border-radius: 50%;
}

.nav-icon--admin::after,
.nav-icon--login::after,
.nav-icon--logout::after {
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    background: currentColor;
    content: '';
}

.page-nav-link:hover {
    text-decoration: underline;
}

.page-nav-link--admin {
    color: #7c2d12;
}

.page-nav-link--auth {
    color: #1d4ed8;
}

.page-nav-link--button {
    padding: 0;
    border: 0;
    background: transparent;
    color: #374151;
    cursor: pointer;
    font: inherit;
}

.language-switcher {
    display: inline-flex;
    overflow: hidden;
    border: 1px solid #d1d5db;
    border-radius: 999px;
}

.language-button {
    padding: 4px 8px;
    border: 0;
    background: #fff;
    color: #4b5563;
    cursor: pointer;
    font: inherit;
    font-size: 12px;
    font-weight: 800;
}

.language-button--active {
    background: #166534;
    color: #fff;
}
</style>
