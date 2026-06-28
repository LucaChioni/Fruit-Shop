<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>
<template>
    <nav class="page-nav">
        <Link :href="route('products.index')" class="page-nav-link">
            Prodotti
        </Link>

        <Link :href="route('cart.index')" class="page-nav-link">
            Carrello
        </Link>

        <Link
            v-if="page.props.auth.user"
            :href="route('orders.index')"
            class="page-nav-link"
        >
            I miei ordini
        </Link>

        <Link
            v-if="page.props.auth.user?.is_admin"
            :href="route('admin.dashboard')"
            class="page-nav-link page-nav-link--admin"
        >
            Admin
        </Link>

        <Link
            v-if="page.props.auth.user?.is_admin"
            :href="route('admin.orders.index')"
            class="page-nav-link page-nav-link--admin"
        >
            Tutti gli ordini
        </Link>

        <Link
            v-if="page.props.auth.user?.is_admin"
            :href="route('admin.products.index')"
            class="page-nav-link page-nav-link--admin"
        >
            Gestione prodotti
        </Link>

        <Link
            v-if="!page.props.auth.user"
            :href="route('login')"
            class="page-nav-link page-nav-link--auth"
        >
            Login
        </Link>

        <Link
            v-if="!page.props.auth.user"
            :href="route('register')"
            class="page-nav-link page-nav-link--auth"
        >
            Registrati
        </Link>

        <Link
            v-if="page.props.auth.user"
            :href="route('logout')"
            method="post"
            as="button"
            class="page-nav-link page-nav-link--button"
        >
            Logout
        </Link>
    </nav>
</template>

<style scoped>
.page-nav {
    display: flex;
    gap: 16px;
    margin-bottom: 12px;
}

.page-nav-link {
    color: #166534;
    font-weight: 600;
    text-decoration: none;
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
</style>
