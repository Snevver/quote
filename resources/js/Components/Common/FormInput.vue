<script setup>
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const handleInput = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
    <div class="form-input-wrapper">
        <label v-if="label" class="form-label">
            {{ label }}
        </label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            @input="handleInput"
            class="form-input"
        />
    </div>
</template>

<style scoped>
@reference "tailwindcss";

.form-input-wrapper {
    @apply w-full;
}

.form-label {
    @apply text-xs font-mono text-white/40 uppercase tracking-widest block mb-2;
}

.form-input {
    @apply w-full bg-transparent border-b border-white/20 py-2 text-sm font-bold text-white font-mono;
    @apply focus:outline-none focus:border-white placeholder:text-white/20 transition-colors;
}

.form-input:disabled {
    @apply opacity-50 cursor-not-allowed;
}
</style>
