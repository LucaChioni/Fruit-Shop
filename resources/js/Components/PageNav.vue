<script setup>
import { onMounted, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useTranslations } from '@/i18n';

const page = usePage();
const t = useTranslations();

defineProps({
    title: {
        type: String,
        default: 'Il Giardino della Frutta',
    },
});

const showLogoutConfirmation = ref(false);
const logoutProcessing = ref(false);
const isDarkMode = ref(false);
const isMobileMenuOpen = ref(false);
const themeStorageKey = 'fruit_shop_theme';

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.style.colorScheme = theme;
    isDarkMode.value = theme === 'dark';
}

function getPreferredTheme() {
    try {
        const storedTheme = window.localStorage.getItem(themeStorageKey);

        if (storedTheme === 'dark' || storedTheme === 'light') {
            return storedTheme;
        }
    } catch {
        // Fall back to the system preference when localStorage is unavailable.
    }

    return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

onMounted(() => {
    applyTheme(getPreferredTheme());
});

function toggleTheme() {
    const nextTheme = isDarkMode.value ? 'light' : 'dark';

    setTheme(nextTheme);
}

function setTheme(theme) {
    if ((theme === 'dark') === isDarkMode.value) {
        return;
    }

    try {
        window.localStorage.setItem(themeStorageKey, theme);
    } catch {
        // The visual change still applies for the current page view.
    }

    applyTheme(theme);
}

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
    <div class="page-nav-shell">
        <h1 class="page-nav-title">{{ title }}</h1>

        <button
            type="button"
            class="page-nav-menu-button"
            :aria-label="t('nav.menu', 'Menu')"
            :aria-expanded="isMobileMenuOpen"
            aria-controls="page-nav-menu"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
        >
            <svg class="page-nav-menu-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M4 6h16" />
                <path d="M4 12h16" />
                <path d="M4 18h16" />
            </svg>
        </button>

        <nav
            id="page-nav-menu"
            class="page-nav"
            :class="{ 'page-nav--open': isMobileMenuOpen }"
        >
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
                v-if="page.props.auth.user"
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
                :class="{ 'page-nav-button--active': route().current('admin.orders.*') }"
                :aria-current="route().current('admin.orders.*') ? 'page' : undefined"
            >
                <span class="nav-icon nav-icon--orders" aria-hidden="true"></span>
                {{ t('admin.manage_orders', 'Gestisci ordini') }}
            </Link>

            <Link
                v-if="page.props.auth.user?.is_admin"
                :href="route('admin.products.index')"
                as="button"
                type="button"
                class="page-nav-button page-nav-button--admin"
                :class="{ 'page-nav-button--active': route().current('admin.products.*') }"
                :aria-current="route().current('admin.products.*') ? 'page' : undefined"
            >
                <svg class="nav-svg-icon nav-svg-icon--admin-products" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <rect x="4" y="4" width="6" height="6" rx="1.4" />
                    <rect x="14" y="4" width="6" height="6" rx="1.4" />
                    <rect x="4" y="14" width="6" height="6" rx="1.4" />
                    <rect x="14" y="14" width="6" height="6" rx="1.4" />
                </svg>
                {{ t('admin.manage_products', 'Gestisci prodotti') }}
            </Link>

            <a
                v-if="page.props.shop?.mapsUrl"
                :href="page.props.shop.mapsUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="page-nav-button page-nav-button--maps"
            >
                <svg class="nav-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z" />
                    <circle cx="12" cy="9" r="2.4" />
                </svg>
                {{ t('nav.maps', 'Indicazioni') }}
            </a>

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

            <span class="theme-switcher" :aria-label="t('nav.theme', 'Tema')">
                <button
                    type="button"
                    class="theme-button"
                    :class="{ 'theme-button--active': !isDarkMode }"
                    :aria-label="t('nav.light_mode', 'Chiaro')"
                    :aria-pressed="!isDarkMode"
                    @click="setTheme('light')"
                >
                    <svg class="theme-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2" />
                        <path d="M12 20v2" />
                        <path d="m4.93 4.93 1.41 1.41" />
                        <path d="m17.66 17.66 1.41 1.41" />
                        <path d="M2 12h2" />
                        <path d="M20 12h2" />
                        <path d="m6.34 17.66-1.41 1.41" />
                        <path d="m19.07 4.93-1.41 1.41" />
                    </svg>
                </button>
                <button
                    type="button"
                    class="theme-button"
                    :class="{ 'theme-button--active': isDarkMode }"
                    :aria-label="t('nav.dark_mode', 'Scuro')"
                    :aria-pressed="isDarkMode"
                    @click="setTheme('dark')"
                >
                    <svg class="theme-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.5 6.5 0 0 0 9.8 9.8Z" />
                    </svg>
                </button>
            </span>

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
    </div>

    <Modal :show="showLogoutConfirmation" max-width="md" @close="closeLogoutConfirmation">
        <div class="logout-modal">
            <h2 class="logout-modal-title">{{ t('logout.confirm_title', 'Confermi il logout?') }}</h2>
            <p class="logout-modal-text">
                {{ t('logout.confirm_text', 'Uscirai dal tuo account. Per ordinare dovrai accedere di nuovo.') }}
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
.page-nav-shell {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 20px;
    width: 100%;
}

.page-nav-title {
    flex: 0 0 auto;
    margin: 0;
    font-size: 26px;
    font-weight: 700;
}

.page-nav-menu-button {
    display: none;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: 0;
    background: transparent;
    color: #166534;
    cursor: pointer;
}

.page-nav-menu-button:hover,
.page-nav-menu-button:focus-visible {
    color: #14532d;
    outline: 2px solid #22c55e;
    outline-offset: 4px;
}

.page-nav-menu-icon {
    width: 22px;
    height: 22px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.page-nav {
    box-sizing: border-box;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 10px 18px;
    width: 100%;
    flex: 1 1 560px;
    margin-bottom: 0;
}

.page-nav-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
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
    width: 132px;
    height: 36px;
    gap: 7px;
    padding: 0 12px;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    background: #fff;
    color: #166534;
    cursor: pointer;
    font: inherit;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    text-decoration: none;
    transition: background 150ms ease, border-color 150ms ease, color 150ms ease, box-shadow 150ms ease;
}

.nav-icon {
    position: relative;
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid currentColor;
    border-radius: 4px;
}

.nav-svg-icon {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.nav-svg-icon--admin-products {
    width: 18px;
    height: 18px;
}

.nav-icon--orders::after {
    position: absolute;
    inset: 3px;
    border-top: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    content: '';
}

.nav-icon--orders {
    width: 16px;
    height: 16px;
}

.nav-icon--orders::after {
    inset: 3px;
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
    width: 154px;
    color: #7c2d12;
}

.page-nav-button--auth {
    color: #1d4ed8;
}

.page-nav-button--settings {
    color: #4b5563;
}

.page-nav-button--maps {
    border-color: #166534;
    background: #166534;
    color: #fff;
}

.page-nav-button--maps:hover,
.page-nav-button--maps:focus-visible {
    border-color: #14532d;
    background: #14532d;
    color: #fff;
}

.page-nav-button--logout {
    color: #374151;
}

.page-nav-button--active.page-nav-button--admin,
.page-nav-button--active.page-nav-button--auth,
.page-nav-button--active.page-nav-button--settings {
    color: #fff;
}

.theme-switcher,
.language-switcher {
    display: inline-flex;
    overflow: hidden;
    border: 1px solid #d1d5db;
    border-radius: 999px;
}

.theme-button,
.language-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    min-height: 26px;
    padding: 3px 8px;
    border: 0;
    background: #fff;
    color: #4b5563;
    cursor: pointer;
    font: inherit;
    font-size: 12px;
    font-weight: 800;
}

.theme-icon {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: currentColor;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2;
}

.theme-button--active,
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
    .page-nav-shell {
        gap: 8px 12px;
    }

    .page-nav-title {
        flex: 1 1 0;
        min-width: 0;
        font-size: 22px;
    }

    .page-nav-menu-button {
        display: inline-flex;
        flex: 0 0 auto;
    }

    .page-nav {
        display: none;
        flex: 1 1 100%;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        border: 1px solid #d1fae5;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 12px 28px rgba(22, 101, 52, 0.12);
    }

    .page-nav--open {
        display: flex;
    }

    .page-nav-group {
        flex: 1 1 100%;
        flex-wrap: wrap;
        width: 100%;
        gap: 8px;
    }

    .page-nav-group--main,
    .page-nav-group--account {
        flex: 0 0 auto;
        justify-content: flex-start;
        margin-left: 0;
    }

    .page-nav-button {
        flex: 1 1 100%;
        width: 100%;
        min-width: 0;
        height: 38px;
        padding: 0 12px;
        font-size: 13px;
    }

    .nav-svg-icon--admin-products {
        width: 16px;
        height: 16px;
    }

    .theme-switcher,
    .language-switcher {
        flex: 0 0 auto;
    }
}
</style>
