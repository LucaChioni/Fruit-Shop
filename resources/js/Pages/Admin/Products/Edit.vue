<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    product: Object,
    unitTypes: Array,
});

const t = useTranslations();

const form = useForm({
    name: props.product.source_name ?? props.product.name,
    description: props.product.source_description ?? props.product.description ?? '',
    image: null,
    remove_image: false,
    price: props.product.price,
    unit_type: props.product.unit_type,
    is_active: props.product.is_active,
});

const imagePreviewUrl = ref(props.product.image_url);
const imageInput = ref(null);
let temporaryImagePreviewUrl = null;

function revokeTemporaryImagePreview() {
    if (temporaryImagePreviewUrl) {
        URL.revokeObjectURL(temporaryImagePreviewUrl);
        temporaryImagePreviewUrl = null;
    }
}

function setImage(event) {
    revokeTemporaryImagePreview();

    form.image = event.target.files[0] ?? null;
    imagePreviewUrl.value = form.remove_image ? null : props.product.image_url;

    if (form.image) {
        form.remove_image = false;
        temporaryImagePreviewUrl = URL.createObjectURL(form.image);
        imagePreviewUrl.value = temporaryImagePreviewUrl;
    }
}

function removeImage() {
    revokeTemporaryImagePreview();

    form.image = null;
    form.remove_image = true;
    imagePreviewUrl.value = null;

    if (imageInput.value) {
        imageInput.value.value = '';
    }
}

onBeforeUnmount(revokeTemporaryImagePreview);

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
                <img
                    v-if="imagePreviewUrl"
                    :src="imagePreviewUrl"
                    :alt="product.name"
                    class="current-image"
                />
                <input ref="imageInput" name="image" type="file" accept="image/*" class="input" @change="setImage" />
                <button
                    v-if="imagePreviewUrl"
                    type="button"
                    class="remove-image-button"
                    @click="removeImage"
                >
                    {{ t('admin.form.remove_image', 'Rimuovi immagine') }}
                </button>
                <span v-if="form.remove_image" class="help-text">{{ t('admin.form.image_removed_help', "L'immagine attuale sarà rimossa al salvataggio.") }}</span>
                <span v-else class="help-text">{{ t('admin.form.keep_image_help', "Lascia vuoto per mantenere l'immagine attuale.") }}</span>
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
                {{ t('admin.form.save_product', 'Salva prodotto') }}
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

.remove-image-button {
    justify-self: start;
    padding: 8px 12px;
    border: 1px solid #b91c1c;
    border-radius: 8px;
    background: #fff;
    color: #b91c1c;
    font-weight: 600;
    cursor: pointer;
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
