<script setup>
import { ref, watch } from "vue";
import axios from 'axios';
import FormInput from "../Common/FormInput.vue";
import FormTextarea from "../Common/FormTextarea.vue";
import { useI18n } from "vue-i18n";
import Modal from "../Common/Modal.vue";
import LanguageToggle from "../Common/LanguageToggle.vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    quote: {
        type: Object,
        default: null,
    },
    folderId: {
        type: [Number, null],
        default: null,
    },
});

const emit = defineEmits(["close", "saved"]);

const { t } = useI18n();

const inputLang = ref("en");

const form = ref({
    quote: "",
    author: "",
    tags_en: "",
    tags_nl: "",
    note_en: "",
    note_nl: "",
});

const isLoading = ref(false);

const resetForm = () => {
    form.value = {
        quote: "",
        author: "",
        tags_en: "",
        tags_nl: "",
        note_en: "",
        note_nl: "",
    };
};

// Watch for quote prop changes to populate form
watch(
    () => props.quote,
    (newQuote) => {
        if (newQuote) {
            form.value = {
                quote: newQuote.quote || "",
                author: newQuote.author || "",
                tags_en: newQuote.tags_en ? newQuote.tags_en.join(", ") : "",
                tags_nl: newQuote.tags_nl ? newQuote.tags_nl.join(", ") : "",
                note_en: newQuote.note_en || "",
                note_nl: newQuote.note_nl || "",
            };
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

const handleClose = () => {
    resetForm();
    emit("close");
};

const handleSave = async () => {
    if (!form.value.quote || !form.value.author) {
        return;
    }

    isLoading.value = true;

    try {
        const payload = {
            quote: form.value.quote,
            author: form.value.author,
            tags_en: form.value.tags_en
                ? form.value.tags_en
                    .split(",")
                    .map((t) => t.trim())
                    .filter((t) => t)
                : [],
            tags_nl: form.value.tags_nl
                ? form.value.tags_nl
                    .split(",")
                    .map((t) => t.trim())
                    .filter((t) => t)
                : [],
            note_en: form.value.note_en || null,
            note_nl: form.value.note_nl || null,
            folder_id: props.quote ? props.quote.folder_id : props.folderId,
        };

        const url = props.quote
            ? `/api/quotes/${props.quote.id}`
            : "/api/quotes";

        const response = await axios.post(url, payload);

        if (response.data.success) {
            emit("saved");
            handleClose();
        }
    } catch (error) {
        console.error("Failed to save quote:", error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Modal :show="show" @close="handleClose">
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center border-b border-white/10 pb-4">
                <h3 class="text-xl font-mono uppercase tracking-wider text-white">
                    {{
                        quote
                            ? $t("pages.quotes.edit_quote")
                            : $t("pages.quotes.add_quote")
                    }}
                </h3>
            </div>

            <!-- Form -->
            <div class="space-y-4">
                <!-- Language Toggle -->
                <div class="flex justify-end mb-4">
                    <LanguageToggle v-model="inputLang" label="INPUT LANGUAGE" />
                </div>

                <!-- Quote -->
                <div>
                    <FormTextarea v-model="form.quote" :rows="4" :placeholder="$t('pages.quotes.quote_placeholder')"
                        :label="$t('pages.quotes.quote') + ' *'" />
                </div>

                <!-- Author -->
                <div>
                    <FormInput v-model="form.author" :placeholder="$t('pages.quotes.author_placeholder')"
                        :label="$t('pages.quotes.author') + ' *'" />
                </div>

                <!-- Tags (EN) -->
                <div v-if="inputLang === 'en'">
                    <label class="block text-xs font-mono text-white/40 uppercase tracking-widest mb-2">
                        {{ $t("pages.quotes.tags") }} (EN)
                    </label>
                    <FormInput v-model="form.tags_en" :placeholder="$t('pages.quotes.tags_placeholder')" />
                    <p class="text-[10px] text-white/30 font-mono mt-1">
                        {{ $t("pages.quotes.tags_hint") }}
                    </p>
                </div>

                <!-- Tags (NL) -->
                <div v-if="inputLang === 'nl'">
                    <label class="block text-xs font-mono text-white/40 uppercase tracking-widest mb-2">
                        {{ $t("pages.quotes.tags") }} (NL)
                    </label>
                    <FormInput v-model="form.tags_nl" :placeholder="$t('pages.quotes.tags_placeholder')" />
                    <p class="text-[10px] text-white/30 font-mono mt-1">
                        {{ $t("pages.quotes.tags_hint") }}
                    </p>
                </div>

                <!-- Note (EN) -->
                <div v-if="inputLang === 'en'">
                    <label class="block text-xs font-mono text-white/40 uppercase tracking-widest mb-2">
                        {{ $t("pages.quotes.note") }} (EN)
                    </label>
                    <FormTextarea v-model="form.note_en" :rows="3" :placeholder="$t('pages.quotes.note_placeholder')"
                        :label="''" />
                </div>

                <!-- Note (NL) -->
                <div v-if="inputLang === 'nl'">
                    <label class="block text-xs font-mono text-white/40 uppercase tracking-widest mb-2">
                        {{ $t("pages.quotes.note") }} (NL)
                    </label>
                    <FormTextarea v-model="form.note_nl" :rows="3" :placeholder="$t('pages.quotes.note_placeholder')"
                        :label="''" />
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-4 pt-4 border-t border-white/10">
                <button @click="handleClose"
                    class="px-6 py-2 text-white/60 hover:text-white font-mono text-xs uppercase tracking-wider transition-colors cursor-pointer">
                    {{ $t("common.cancel") }}
                </button>
                <button @click="handleSave" :disabled="isLoading || !form.quote || !form.author"
                    class="px-6 py-2 bg-white text-black font-mono text-xs uppercase tracking-wider hover:bg-white/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    {{ isLoading ? $t("common.saving") : $t("common.save") }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
@reference "tailwindcss";
</style>
