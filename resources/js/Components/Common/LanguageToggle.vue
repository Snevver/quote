<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: 'LANGUAGE',
    },
});

const emit = defineEmits(['update:modelValue']);

const toggle = () => {
    emit('update:modelValue', props.modelValue === 'en' ? 'nl' : 'en');
};
</script>

<template>
    <div class="flex items-center gap-3">
        <span class="text-[10px] font-mono text-white/40 tracking-widest uppercase">{{ label }}</span>
        <button
            @click="toggle"
            class="relative flex items-center bg-black/40 border border-white/10 rounded-full px-1 py-1 w-12 h-6 cursor-pointer transition-colors hover:border-white/30"
        >
            <div
                class="absolute w-4 h-4 bg-white rounded-full transition-all duration-300"
                :class="modelValue === 'nl' ? 'translate-x-[24px]' : 'translate-x-0'"
            ></div>
            <span
                class="absolute left-1.5 text-[8px] font-mono font-bold text-white transition-opacity duration-300"
                :class="modelValue === 'nl' ? 'opacity-0' : 'opacity-100'"
            >
                EN
            </span>
            <span
                class="absolute right-1.5 text-[8px] font-mono font-bold text-white transition-opacity duration-300"
                :class="modelValue === 'nl' ? 'opacity-100' : 'opacity-0'"
            >
                NL
            </span>
        </button>
    </div>
</template>
