<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useTranslations } from '@/i18n';

const t = useTranslations();
const isVisible = ref(false);
const storageKey = 'fruit_shop_cookie_notice_accepted';

onMounted(() => {
    try {
        isVisible.value = window.localStorage.getItem(storageKey) !== '1';
    } catch {
        isVisible.value = true;
    }
});

function acceptCookies() {
    try {
        window.localStorage.setItem(storageKey, '1');
    } catch {
        // If storage is unavailable, hide the notice for the current page view.
    }

    isVisible.value = false;
}
</script>

<template>
    <section v-if="isVisible" class="cookie-banner" role="region" :aria-label="t('cookies.banner_label', 'Avviso cookie')">
        <button
            type="button"
            class="cookie-banner-close"
            :aria-label="t('cookies.close', 'Chiudi avviso cookie')"
            @click="acceptCookies"
        >
            ×
        </button>

        <div class="cookie-banner-content">
            <p class="cookie-banner-title">
                {{ t('cookies.banner_title', 'Usiamo cookie tecnici') }}
            </p>

            <p class="cookie-banner-text">
                {{ t('cookies.banner_text', 'Questo sito usa cookie necessari per sessione, sicurezza, login e carrello. Non usiamo cookie pubblicitari o di profilazione.') }}
            </p>
        </div>

        <div class="cookie-banner-actions">
            <Link :href="route('legal.cookies')" class="cookie-banner-link">
                {{ t('cookies.learn_more', 'Leggi la policy') }}
            </Link>

            <button type="button" class="cookie-banner-button" @click="acceptCookies">
                {{ t('cookies.accept', 'Ho capito') }}
            </button>
        </div>
    </section>
</template>

<style scoped>
.cookie-banner {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 50;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    gap: 20px;
    width: min(720px, calc(100vw - 40px));
    padding: 20px 56px 20px 20px;
    border: 1px solid #bbf7d0;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
}

.cookie-banner-close {
    position: absolute;
    top: 10px;
    right: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: #4b5563;
    cursor: pointer;
    font: inherit;
    font-size: 22px;
    line-height: 1;
}

.cookie-banner-close:hover,
.cookie-banner-close:focus-visible {
    background: #f3f4f6;
    color: #111827;
    outline: none;
}

.cookie-banner-content {
    min-width: 0;
}

.cookie-banner-title {
    margin: 0 0 6px;
    color: #111827;
    font-weight: 800;
}

.cookie-banner-text {
    margin: 0;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.45;
}

.cookie-banner-actions {
    display: flex;
    flex: 0 0 auto;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

.cookie-banner-link {
    color: #166534;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}

.cookie-banner-link:hover,
.cookie-banner-link:focus-visible {
    text-decoration: underline;
}

.cookie-banner-button {
    padding: 10px 16px;
    border: 0;
    border-radius: 999px;
    background: #166534;
    color: #fff;
    cursor: pointer;
    font: inherit;
    font-weight: 800;
}

.cookie-banner-button:hover,
.cookie-banner-button:focus-visible {
    background: #14532d;
    outline: none;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.22);
}

@media (max-width: 640px) {
    .cookie-banner {
        right: 12px;
        bottom: 12px;
        left: 12px;
        display: grid;
        width: auto;
        padding: 20px 50px 20px 18px;
    }

    .cookie-banner-actions {
        justify-content: flex-start;
    }
}
</style>
