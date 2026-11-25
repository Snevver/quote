<script setup>
const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    label: { type: String, default: '' },
    rows: { type: Number, default: 3 },
});

const emit = defineEmits(['update:modelValue']);

const handleInput = (e) => emit('update:modelValue', e.target.value);
</script>

<template>
  <div class="w-full">
    <label v-if="label" class="text-xs font-mono text-white/40 uppercase tracking-widest block mb-2">{{ label }}</label>
    <textarea :rows="rows" :placeholder="placeholder" :value="modelValue" @input="handleInput" class="w-full bg-transparent border-b border-white/20 py-2 text-sm font-bold text-white font-mono focus:outline-none placeholder:text-white/20 transition-colors resize-none"></textarea>
  </div>
</template>

<style scoped>
@reference "tailwindcss";
</style>
