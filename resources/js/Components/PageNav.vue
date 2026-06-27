<template>
    <nav class="page-nav">
        <a :href="route('products.index')" class="page-nav-link">
            Prodotti
        </a>

        <a :href="route('cart.index')" class="page-nav-link">
            Carrello
        </a>

        <a
            v-if="$page.props.auth.user"
            :href="route('orders.index')"
            class="page-nav-link"
        >
            I miei ordini
        </a>
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
</style>