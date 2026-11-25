<script setup>
import { ref, watch } from 'vue';
import FormInput from '../Common/FormInput.vue';
import axios from 'axios';

const props = defineProps({ 
    show: Boolean, 
    parentId: { type: [Number, null], default: null },
    folder: { type: Object, default: null } 
});
const emit = defineEmits(['close', 'created', 'updated']);

const name = ref('');
const saving = ref(false);

watch(() => props.show, (isShowing) => {
    if (isShowing) {
        name.value = props.folder ? props.folder.name : '';
    }
});

const close = () => {
    name.value = '';
    emit('close');
};

const submit = async () => {
    if (!name.value) return;
    saving.value = true;
    try {
        if (props.folder) {
            const res = await axios.post(`/api/quote-folders/${props.folder.id}`, { name: name.value });
            emit('updated', res.data.folder || null);
        } else {
            const res = await axios.post('/api/quote-folders', { name: name.value, parent_id: props.parentId });
            emit('created', res.data.folder || null);
        }
        close();
    } catch (e) {
        console.error('Failed processing folder', e);
    } finally {
        saving.value = false;
    }
};

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60" @click="close"></div>
        <div class="relative bg-black/90 border border-white/10 rounded p-6 w-full max-w-md">
            <h3 class="font-mono text-white/80 mb-4">{{ folder ? $t('common.edit') : $t('pages.quotes.create_folder') }}</h3>
            <FormInput v-model="name" :placeholder="$t('pages.quotes.new_folder_placeholder')" :label="$t('pages.quotes.new_folder_name')" />
            <div class="mt-4 flex justify-end gap-3">
                <button class="px-4 py-2 hover:bg-white/5 transition-colors hover:cursor-pointer" @click="close">{{ $t('common.cancel') }}</button>
                <button class="px-4 py-2 bg-white text-black hover:bg-white/90 transition-colors hover:cursor-pointer" @click="submit" :disabled="saving">{{ $t('common.save') }}</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference "tailwindcss";
</style>
