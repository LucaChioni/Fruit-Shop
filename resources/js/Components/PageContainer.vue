<template>
    <main class="page-container">
        <slot />
    </main>
</template>

<style scoped>
.page-container {
    width: min(1120px, calc(100% - 32px));
    margin: 0 auto;
    padding: 40px 0;
}
</style>
