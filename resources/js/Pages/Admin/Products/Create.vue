<script setup>
import { useForm } from '@inertiajs/vue3';
import PageNav from '@/Components/PageNav.vue';

const form = useForm({
    name: '',
    description: '',
    price: '',
    unit_type: 'kg',
    is_active: true,
});

function submit() {
    form.post(route('admin.products.store'));
}
</script>

<template>
    <main class="product-form-page">
        <header class="product-form-header">
            <h1 class="product-form-title">Nuovo prodotto</h1>

            <PageNav />
        </header>

        <form class="product-form" @submit.prevent="submit">
            <label class="field">
                Nome
                <input v-model="form.name" type="text" class="input" />
                <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
            </label>

            <label class="field">
                Descrizione
                <textarea v-model="form.description" class="input textarea"></textarea>
                <span v-if="form.errors.description" class="error">{{ form.errors.description }}</span>
            </label>

            <label class="field">
                Prezzo
                <input v-model="form.price" type="number" min="0" step="0.01" class="input" />
                <span v-if="form.errors.price" class="error">{{ form.errors.price }}</span>
            </label>

            <label class="field">
                Unità di misura
                <input v-model="form.unit_type" type="text" class="input" />
                <span v-if="form.errors.unit_type" class="error">{{ form.errors.unit_type }}</span>
            </label>

            <label class="checkbox-field">
                <input v-model="form.is_active" type="checkbox" />
                Prodotto attivo
            </label>

            <button type="submit" class="submit-button" :disabled="form.processing">
                Crea prodotto
            </button>
        </form>
    </main>
</template>

<style scoped>
.product-form-page {
    padding: 40px;
}

.product-form-header {
    margin-bottom: 24px;
}

.product-form-title {
    margin: 0 0 8px;
    font-size: 28px;
    font-weight: 700;
}

.product-form {
    display: grid;
    max-width: 640px;
    gap: 16px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    background: #fff;
}

.field {
    display: grid;
    gap: 6px;
    font-weight: 600;
}

.checkbox-field {
    display: flex;
    gap: 8px;
    align-items: center;
    font-weight: 600;
}

.input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
}

.textarea {
    min-height: 100px;
}

.submit-button {
    justify-self: start;
    padding: 10px 16px;
    border: 0;
    border-radius: 8px;
    background: #7c2d12;
    color: white;
    font-weight: 600;
    cursor: pointer;
}

.submit-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error {
    color: #b91c1c;
    font-weight: 600;
}
</style>
