<script setup>
import Modal from './Modal.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmLabel: { type: String, default: 'CONFIRM' },
    cancelLabel: { type: String, default: 'CANCEL' },
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
    emit('close');
};

const confirm = () => {
    emit('confirm');
    close();
};
</script>

<template>
    <Modal :show="show" maxWidth="md" @close="close">
        <div class="text-center space-y-4">
            <h3 class="text-xl font-bold font-mono tracking-widest uppercase text-white">
                {{ title }}
            </h3>
            
            <p class="text-white/70 font-mono text-sm whitespace-pre-line">
                {{ message }}
            </p>

            <div class="flex justify-center gap-4 pt-4">
                <button 
                    @click="close"
                    class="px-4 py-2 text-white/50 hover:text-white font-mono text-xs uppercase cursor-pointer transition-colors"
                >
                    {{ cancelLabel }}
                </button>
                <button
                    @click="confirm"
                    class="px-6 py-2 bg-red-500/80 text-white font-mono text-xs font-bold uppercase hover:bg-red-500 transition-colors border border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.3)] hover:cursor-pointer"
                >
                    {{ confirmLabel }}
                </button>
            </div>
        </div>
    </Modal>
</template>
