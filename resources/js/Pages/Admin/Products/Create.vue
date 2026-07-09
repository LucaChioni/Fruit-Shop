<script setup>
import { useForm } from '@inertiajs/vue3';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

defineProps({
    unitTypes: Array,
});

const t = useTranslations();

const form = useForm({
    name: '',
    description: '',
    image: null,
    price: '',
    unit_type: 'kg',
    is_active: true,
});

function setImage(event) {
    form.image = event.target.files[0] ?? null;
}

function submit() {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <PageContainer>
        <header class="product-form-header">
            <h1 class="product-form-title">Il Giardino della Frutta</h1>

            <PageNav />
        </header>

        <form class="product-form" enctype="multipart/form-data" @submit.prevent="submit">
            <label class="field">
                {{ t('admin.form.name', 'Nome') }}
                <input v-model="form.name" type="text" class="input" />
                <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.description', 'Descrizione') }}
                <textarea v-model="form.description" class="input textarea"></textarea>
                <span v-if="form.errors.description" class="error">{{ form.errors.description }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.image', 'Immagine') }}
                <input name="image" type="file" accept="image/*" class="input" @change="setImage" />
                <span v-if="form.errors.image" class="error">{{ form.errors.image }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.price', 'Prezzo') }}
                <input v-model="form.price" type="number" min="0" step="0.01" class="input" />
                <span v-if="form.errors.price" class="error">{{ form.errors.price }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.unit_type', 'Unità di misura') }}
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
                {{ t('admin.form.active_product', 'Prodotto attivo') }}
            </label>

            <button type="submit" class="submit-button" :disabled="form.processing">
                {{ t('admin.form.create_product', 'Crea prodotto') }}
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
