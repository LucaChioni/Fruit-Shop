<script setup>
import { useForm } from '@inertiajs/vue3';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';

const props = defineProps({
    product: Object,
    unitTypes: Array,
});

const form = useForm({
    name: props.product.name,
    description: props.product.description ?? '',
    image: null,
    price: props.product.price,
    unit_type: props.product.unit_type,
    is_active: props.product.is_active,
});

function setImage(event) {
    form.image = event.target.files[0] ?? null;
}

function submit() {
    form.transform((data) => ({
        ...data,
        _method: 'patch',
    })).post(route('admin.products.update', props.product.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="product-form-header">
            <h1 class="product-form-title">Modifica prodotto</h1>

            <PageNav />
        </header>

        <form class="product-form" enctype="multipart/form-data" @submit.prevent="submit">
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
                Immagine
                <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="current-image"
                />
                <input name="image" type="file" accept="image/*" class="input" @change="setImage" />
                <span class="help-text">Lascia vuoto per mantenere l'immagine attuale.</span>
                <span v-if="form.errors.image" class="error">{{ form.errors.image }}</span>
            </label>

            <label class="field">
                Prezzo
                <input v-model="form.price" type="number" min="0" step="0.01" class="input" />
                <span v-if="form.errors.price" class="error">{{ form.errors.price }}</span>
            </label>

            <label class="field">
                Unità di misura
                <select v-model="form.unit_type" class="input">
                    <option
                        v-for="unitType in unitTypes"
                        :key="unitType"
                        :value="unitType"
                    >
                        {{ unitType }}
                    </option>
                </select>
                <span v-if="form.errors.unit_type" class="error">{{ form.errors.unit_type }}</span>
            </label>

            <label class="checkbox-field">
                <input v-model="form.is_active" type="checkbox" />
                Prodotto attivo
            </label>

            <button type="submit" class="submit-button" :disabled="form.processing">
                Salva prodotto
            </button>
        </form>
    </PageContainer>
</template>

<style scoped>
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

.current-image {
    width: 160px;
    height: 100px;
    border-radius: 8px;
    object-fit: cover;
    background: #fff7ed;
}

.help-text {
    color: #6b7280;
    font-size: 14px;
    font-weight: 400;
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
