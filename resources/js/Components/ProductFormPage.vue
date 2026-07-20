<script setup>
import { useForm } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import PageNav from '@/Components/PageNav.vue';
import PageContainer from '@/Components/PageContainer.vue';
import { useTranslations } from '@/i18n';

const props = defineProps({
    mode: {
        type: String,
        required: true,
        validator: (value) => ['create', 'edit'].includes(value),
    },
    product: {
        type: Object,
        default: null,
    },
    unitTypes: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const t = useTranslations();
const isEdit = props.mode === 'edit';

const form = useForm(isEdit ? {
    name: props.product.source_name ?? props.product.name,
    name_en: props.product.name_en ?? '',
    description: props.product.source_description ?? props.product.description ?? '',
    description_en: props.product.description_en ?? '',
    category: props.product.category,
    image: null,
    remove_image: false,
    price: props.product.price,
    unit_type: props.product.unit_type,
    is_active: props.product.is_active,
} : {
    name: '',
    name_en: '',
    description: '',
    description_en: '',
    category: 'fruit',
    image: null,
    price: '',
    unit_type: 'kg',
    is_active: true,
});

const imagePreviewUrl = ref(isEdit ? props.product.image_url : null);
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
    imagePreviewUrl.value = isEdit && !form.remove_image ? props.product.image_url : null;

    if (form.image) {
        if (isEdit) {
            form.remove_image = false;
        }

        temporaryImagePreviewUrl = URL.createObjectURL(form.image);
        imagePreviewUrl.value = temporaryImagePreviewUrl;
    }
}

function removeImage() {
    revokeTemporaryImagePreview();

    form.image = null;

    if (isEdit) {
        form.remove_image = true;
    }

    imagePreviewUrl.value = null;

    if (imageInput.value) {
        imageInput.value.value = '';
    }
}

onBeforeUnmount(revokeTemporaryImagePreview);

function submit() {
    if (isEdit) {
        form.transform((data) => ({
            ...data,
            _method: 'patch',
        })).post(route('admin.products.update', props.product.id), {
            forceFormData: true,
        });

        return;
    }

    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <PageContainer narrow>
        <header class="product-form-header page-header">
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

            <label class="field">
                {{ t('admin.form.category', 'Categoria') }}
                <select v-model="form.category" class="input">
                    <option v-for="category in categories" :key="category" :value="category">
                        {{ t(`categories.${category}`, category) }}
                    </option>
                </select>
                <span v-if="form.errors.category" class="error">{{ form.errors.category }}</span>
            </label>

            <div class="field">
                <span>{{ t('admin.form.image', 'Immagine') }}</span>
                <div class="image-field-body">
                    <div class="image-actions">
                        <button type="button" class="file-button" @click="imageInput?.click()">
                            {{ t('admin.form.choose_image', 'Scegli immagine') }}
                        </button>
                        <button
                            v-if="imagePreviewUrl"
                            type="button"
                            class="remove-image-button"
                            @click="removeImage"
                        >
                            {{ t('admin.form.remove_image', 'Rimuovi immagine') }}
                        </button>
                    </div>
                    <img
                        v-if="imagePreviewUrl"
                        :src="imagePreviewUrl"
                        :alt="isEdit ? product.name : form.name || t('admin.form.image', 'Immagine')"
                        class="current-image"
                    />
                    <div v-else class="image-placeholder">
                        {{ t('admin.form.no_image_selected', 'Nessun file selezionato') }}
                    </div>
                </div>
                <input ref="imageInput" name="image" type="file" accept="image/*" class="file-input" tabindex="-1" @change="setImage" />
                <span v-if="isEdit && form.remove_image" class="help-text">{{ t('admin.form.image_removed_help', "L'immagine attuale sarà rimossa al salvataggio.") }}</span>
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
                {{ isEdit ? t('admin.form.save_product', 'Salva prodotto') : t('admin.form.create_product', 'Crea prodotto') }}
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
    width: 218px;
    height: 153px;
    border-radius: 8px;
    object-fit: cover;
    background: #fff7ed;
}

.image-placeholder {
    display: grid;
    place-items: center;
    width: 218px;
    height: 153px;
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
    grid-template-rows: repeat(2, minmax(0, 1fr));
    justify-items: start;
    gap: 8px;
    height: 153px;
}

.file-button,
.remove-image-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 160px;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font: inherit;
}

.file-button {
    border: 1px solid #ccc;
    background: #fff;
    color: #111827;
}

.file-button:hover,
.file-button:focus-visible {
    border-color: #9a3412;
    background: #fff7ed;
    outline: none;
}

.remove-image-button {
    justify-self: start;
    border: 1px solid #b91c1c;
    background: #fff;
    color: #b91c1c;
}

.remove-image-button:hover,
.remove-image-button:focus-visible {
    background: #fee2e2;
    outline: none;
}

.file-input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
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
