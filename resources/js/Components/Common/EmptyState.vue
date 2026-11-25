<script setup>
defineProps({
    message: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="empty-state">
        <div class="text-white/50 font-mono text-sm uppercase tracking-widest">
            [{{ message }}]
        </div>
    </div>
</template>

<style scoped>
.empty-state {
    padding: 3rem 0;
    text-align: center;
}
</style>
