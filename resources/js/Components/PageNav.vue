<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useTranslations } from '@/i18n';

const page = usePage();
const t = useTranslations();

const showLogoutConfirmation = ref(false);
const logoutProcessing = ref(false);

function confirmLogout() {
    showLogoutConfirmation.value = true;
}

function closeLogoutConfirmation() {
    if (!logoutProcessing.value) {
        showLogoutConfirmation.value = false;
    }
}

function logout() {
    router.post(route('logout'), {}, {
        onStart: () => {
            logoutProcessing.value = true;
        },
        onFinish: () => {
            logoutProcessing.value = false;
            showLogoutConfirmation.value = false;
        },
    });
}
</script>
<template>
    <nav class="page-nav">
        <div class="page-nav-group page-nav-group--main">
            <Link
                :href="route('products.index')"
                as="button"
                type="button"
                class="page-nav-button"
                :class="{
                    'page-nav-button--active': route().current('dashboard') || route().current('products.index'),
                }"
                :aria-current="(route().current('dashboard') || route().current('products.index')) ? 'page' : undefined"
            >
                <svg class="nav-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <rect x="4" y="4" width="6" height="6" rx="1.4" />
                    <rect x="14" y="4" width="6" height="6" rx="1.4" />
                    <rect x="4" y="14" width="6" height="6" rx="1.4" />
                    <rect x="14" y="14" width="6" height="6" rx="1.4" />
                </svg>
                {{ t('nav.products', 'Prodotti') }}
            </Link>

            <Link
                :href="route('cart.index')"
                as="button"
                type="button"
                class="page-nav-button"
                :class="{
                    'page-nav-button--active': route().current('cart.*') || route().current('checkout.*'),
                }"
                :aria-current="(route().current('cart.*') || route().current('checkout.*')) ? 'page' : undefined"
            >
                <svg class="nav-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <circle cx="9" cy="20" r="1.7" />
                    <circle cx="18" cy="20" r="1.7" />
                    <path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6" />
                </svg>
                {{ t('nav.cart', 'Carrello') }}
            </Link>

            <Link
                v-if="page.props.auth.user"
                :href="route('orders.index')"
                as="button"
                type="button"
                class="page-nav-button"
                :class="{ 'page-nav-button--active': route().current('orders.*') }"
                :aria-current="route().current('orders.*') ? 'page' : undefined"
            >
                <span class="nav-icon nav-icon--orders" aria-hidden="true"></span>
                {{ t('nav.orders', 'I miei ordini') }}
            </Link>
        </div>

        <div class="page-nav-group page-nav-group--account">
            <Link
                v-if="page.props.auth.user?.is_admin"
                :href="route('admin.orders.index')"
                as="button"
                type="button"
                class="page-nav-button page-nav-button--admin"
                :class="{ 'page-nav-button--active': route().current('admin.*') }"
                :aria-current="route().current('admin.*') ? 'page' : undefined"
            >
                <span class="nav-icon nav-icon--admin" aria-hidden="true"></span>
                {{ t('nav.admin', 'Admin') }}
            </Link>

            <Link
                v-if="page.props.auth.user"
                :href="route('profile.edit')"
                as="button"
                type="button"
                class="page-nav-button page-nav-button--settings"
                :class="{ 'page-nav-button--active': route().current('profile.*') }"
                :aria-current="route().current('profile.*') ? 'page' : undefined"
            >
                <svg class="nav-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" />
                    <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09A1.7 1.7 0 0 0 19.4 15Z" />
                </svg>
                {{ t('nav.settings', 'Impostazioni') }}
            </Link>

            <Link
                v-if="!page.props.auth.user"
                :href="route('login')"
                as="button"
                type="button"
                class="page-nav-button page-nav-button--auth"
                :class="{ 'page-nav-button--active': route().current('login') }"
                :aria-current="route().current('login') ? 'page' : undefined"
            >
                <span class="nav-icon nav-icon--login" aria-hidden="true"></span>
                {{ t('nav.login', 'Login') }}
            </Link>

            <Link
                v-if="!page.props.auth.user"
                :href="route('register')"
                as="button"
                type="button"
                class="page-nav-button page-nav-button--auth"
                :class="{ 'page-nav-button--active': route().current('register') }"
                :aria-current="route().current('register') ? 'page' : undefined"
            >
                <span class="nav-icon nav-icon--register" aria-hidden="true"></span>
                {{ t('nav.register', 'Registrati') }}
            </Link>

            <button
                v-if="page.props.auth.user"
                type="button"
                class="page-nav-button page-nav-button--logout"
                @click="confirmLogout"
            >
                <svg class="nav-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <path d="M16 17l5-5-5-5" />
                    <path d="M21 12H9" />
                </svg>
                {{ t('nav.logout', 'Logout') }}
            </button>

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
        </div>
    </nav>

    <Modal :show="showLogoutConfirmation" max-width="md" @close="closeLogoutConfirmation">
        <div class="logout-modal">
            <h2 class="logout-modal-title">{{ t('logout.confirm_title', 'Confermi il logout?') }}</h2>
            <p class="logout-modal-text">
                {{ t('logout.confirm_text', 'Uscirai dal tuo account e potrai continuare a navigare come ospite.') }}
            </p>

            <div class="logout-modal-actions">
                <SecondaryButton :disabled="logoutProcessing" @click="closeLogoutConfirmation">
                    {{ t('logout.cancel', 'Annulla') }}
                </SecondaryButton>

                <DangerButton
                    type="button"
                    :disabled="logoutProcessing"
                    :class="{ 'opacity-25': logoutProcessing }"
                    @click="logout"
                >
                    {{ t('nav.logout', 'Logout') }}
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.page-nav {
    box-sizing: border-box;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 16px 32px;
    width: 100vw;
    margin-right: calc(50% - 50vw);
    margin-bottom: 12px;
    margin-left: calc(50% - 50vw);
    padding: 0 24px;
}

.page-nav-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px;
}

.page-nav-group--main {
    flex: 0 0 auto;
}

.page-nav-group--account {
    flex: 1 1 360px;
    justify-content: flex-end;
    margin-left: auto;
}

.page-nav-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 148px;
    height: 42px;
    gap: 8px;
    padding: 0 14px;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    background: #fff;
    color: #166534;
    cursor: pointer;
    font: inherit;
    font-weight: 600;
    text-decoration: none;
    transition: background 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
}

.nav-icon {
    position: relative;
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid currentColor;
    border-radius: 4px;
}

.nav-svg-icon {
    width: 18px;
    height: 18px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.nav-icon--orders::after,
.nav-icon--register::after {
    position: absolute;
    inset: 3px;
    border-top: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    content: '';
}

.nav-icon--admin,
.nav-icon--login {
    border-radius: 50%;
}

.nav-icon--admin::after,
.nav-icon--login::after {
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    background: currentColor;
    content: '';
}

.page-nav-button:hover,
.page-nav-button:focus-visible {
    border-color: #22c55e;
    background: #f0fdf4;
    outline: none;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18);
}

.page-nav-button--active {
    border-color: #166534;
    background: #166534;
    color: #fff;
    box-shadow: 0 8px 18px rgba(22, 101, 52, 0.18);
}

.page-nav-button--active:hover,
.page-nav-button--active:focus-visible {
    border-color: #14532d;
    background: #14532d;
}

.page-nav-button--admin {
    color: #7c2d12;
}

.page-nav-button--auth {
    color: #1d4ed8;
}

.page-nav-button--settings {
    color: #4b5563;
}

.page-nav-button--logout {
    color: #374151;
}

.page-nav-button--active.page-nav-button--admin,
.page-nav-button--active.page-nav-button--auth,
.page-nav-button--active.page-nav-button--settings {
    color: #fff;
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

.logout-modal {
    padding: 24px;
}

.logout-modal-title {
    margin: 0;
    color: #111827;
    font-size: 20px;
    font-weight: 700;
}

.logout-modal-text {
    margin: 12px 0 0;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.5;
}

.logout-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

@media (max-width: 640px) {
    .page-nav {
        gap: 10px;
        width: 100%;
        margin-right: 0;
        margin-left: 0;
        padding: 0;
    }

    .page-nav-group {
        width: 100%;
        gap: 8px;
    }

    .page-nav-group--main,
    .page-nav-group--account {
        flex: 1 1 100%;
        justify-content: flex-start;
        margin-left: 0;
    }

    .page-nav-button {
        flex: 1 1 calc(50% - 8px);
        width: auto;
        min-width: 0;
        padding: 0 10px;
        font-size: 14px;
    }

    .language-switcher {
        flex: 0 0 auto;
    }
}
</style>
