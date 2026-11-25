<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    isEditing: Boolean
});

const emit = defineEmits(['toggle']);
const { t } = useI18n();
</script>

<template>
    <button
        @click="emit('toggle')"
        class="bg-white/10 hover:bg-white/20 border border-white/20 px-3 py-1.5 sm:px-4 sm:py-2 text-white font-mono text-[8px] sm:text-[10px] uppercase tracking-[0.2em] transition-all cursor-pointer whitespace-nowrap"
    >
        {{ isEditing ? $t('common.exit_edit') : $t('common.edit_mode') }}
    </button>
</template>
