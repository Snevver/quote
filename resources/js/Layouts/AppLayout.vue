<script setup>
    import { inject } from "vue";
    import axios from "axios";
    import LanguageSwitcher from "../Components/Common/LanguageSwitcher.vue";

    const isAuthenticated = inject("auth", false);

    const logout = async () => {
        try {
            await axios.post("/logout");
        } finally {
            window.location.href = "/inchecken";
        }
    };
</script>

<template>
    <div class="min-h-screen bg-black text-white flex flex-col">
        <header class="sticky top-0 z-50 px-4 sm:px-6 py-4 border-b border-white/10 bg-black/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 page-header">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-1 h-8 bg-white/80 shadow-[0_0_15px_rgba(255,255,255,0.5)] shrink-0"></div>
                    <div class="min-w-0 w-full truncate">
                        <slot name="header" />
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <LanguageSwitcher />
                    <button
                        v-if="isAuthenticated"
                        @click="logout"
                        class="px-3 py-1.5 border border-red-500/30 hover:border-red-500/60 bg-red-500/5 hover:bg-red-500/10 text-[10px] font-mono uppercase tracking-[0.2em] text-red-400/80 hover:text-red-300 transition-colors cursor-pointer"
                    >
                        {{ $t("menu.logout") }}
                    </button>
                </div>
            </div>
        </header>

        <main class="grow relative">
            <slot name="body" />
        </main>
    </div>
</template>

<style scoped>
:deep(.page-header h2) {
    font-size: 1rem !important;
    line-height: 1.1 !important;
}
@media (min-width: 640px) {
    :deep(.page-header h2) {
        font-size: 1.25rem !important;
    }
}
@media (min-width: 1024px) {
    :deep(.page-header h2) {
        font-size: 1.5rem !important;
    }
}
</style>
