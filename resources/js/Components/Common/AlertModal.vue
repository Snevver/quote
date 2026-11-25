<script setup>
import Modal from './Modal.vue';

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    type: {
        type: String,
        default: 'info' // info, success, error
    }
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" maxWidth="md" @close="close">
        <div class="text-center space-y-4">
            <h3 class="text-xl font-bold font-mono tracking-widest uppercase"
                :class="{
                    'text-red-500': type === 'error',
                    'text-green-400': type === 'success',
                    'text-white': type === 'info'
                }">
                {{ title }}
            </h3>
            
            <p class="text-white/70 font-mono text-sm whitespace-pre-line">
                {{ message }}
            </p>

            <div class="pt-4">
                <button 
                    @click="close"
                    class="px-6 py-2 bg-white/10 text-white font-mono text-xs font-bold uppercase hover:bg-white/20 transition-colors border border-white/10 hover:cursor-pointer"
                >
                    ACKNOWLEDGE
                </button>
            </div>
        </div>
    </Modal>
</template>
