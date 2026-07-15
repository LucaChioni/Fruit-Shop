<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

defineProps({
    unitTypes: Array,
});

const t = useTranslations();
const imageInput = ref(null);
const imagePreviewUrl = ref(null);
let temporaryImagePreviewUrl = null;

function revokeTemporaryImagePreview() {
    if (temporaryImagePreviewUrl) {
        URL.revokeObjectURL(temporaryImagePreviewUrl);
        temporaryImagePreviewUrl = null;
    }
}

const form = useForm({
    name: '',
    name_en: '',
    description: '',
    description_en: '',
    image: null,
    price: '',
    unit_type: 'kg',
    is_active: true,
});

function setImage(event) {
    revokeTemporaryImagePreview();

    form.image = event.target.files[0] ?? null;

    if (form.image) {
        temporaryImagePreviewUrl = URL.createObjectURL(form.image);
        imagePreviewUrl.value = temporaryImagePreviewUrl;
    } else {
        imagePreviewUrl.value = null;
    }
}

onBeforeUnmount(revokeTemporaryImagePreview);

function submit() {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <PageContainer narrow>
        <header class="product-form-header">
            <PageNav />

        </header>

        <form class="product-form" enctype="multipart/form-data" @submit.prevent="submit">
            <label class="field">
                {{ t('admin.form.name', 'Nome') }}
                <input v-model="form.name" type="text" class="input" />
                <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.name_en', 'Nome in inglese') }}
                <input v-model="form.name_en" type="text" class="input" />
                <span v-if="form.errors.name_en" class="error">{{ form.errors.name_en }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.description', 'Descrizione') }}
                <textarea v-model="form.description" class="input textarea"></textarea>
                <span v-if="form.errors.description" class="error">{{ form.errors.description }}</span>
            </label>

            <label class="field">
                {{ t('admin.form.description_en', 'Descrizione in inglese') }}
                <textarea v-model="form.description_en" class="input textarea"></textarea>
                <span v-if="form.errors.description_en" class="error">{{ form.errors.description_en }}</span>
            </label>

            <div class="field">
                <span>{{ t('admin.form.image', 'Immagine') }}</span>
                <div class="image-field-body">
                    <div class="image-actions">
                        <button type="button" class="file-button" @click="imageInput?.click()">
                            {{ t('admin.form.choose_image', 'Scegli immagine') }}
                        </button>
                    </div>
                    <img
                        v-if="imagePreviewUrl"
                        :src="imagePreviewUrl"
                        :alt="form.name || t('admin.form.image', 'Immagine')"
                        class="current-image"
                    />
                    <div v-else class="image-placeholder">
                        {{ t('admin.form.no_image_selected', 'Nessun file selezionato') }}
                    </div>
                </div>
                <input ref="imageInput" name="image" type="file" accept="image/*" class="file-input" tabindex="-1" @change="setImage" />
                <span v-if="form.errors.image" class="error">{{ form.errors.image }}</span>
            </div>

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
                        {{ t(`units.${unitType}`, unitType) }}
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

.current-image {
    width: 160px;
    height: 160px;
    border-radius: 8px;
    object-fit: cover;
    background: #fff7ed;
}

.image-placeholder {
    display: grid;
    place-items: center;
    width: 160px;
    height: 160px;
    box-sizing: border-box;
    padding: 10px;
    border: 1px dashed #ccc;
    border-radius: 8px;
    background: #fff7ed;
    color: #6b7280;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
}

.image-field-body {
    display: flex;
    flex-wrap: wrap;
    align-items: start;
    gap: 12px;
}

.image-actions {
    display: grid;
    justify-items: start;
    gap: 8px;
}

.file-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 160px;
    min-height: 38px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fff;
    color: #111827;
    font-weight: 600;
    cursor: pointer;
    font: inherit;
}

.file-input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
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
