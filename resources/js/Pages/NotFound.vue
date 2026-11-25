<script setup>
import AppLayout from "../Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import { portfolioUrl } from "../config/domains";

const { t } = useI18n();

const glitchText = ref("404");
const currentMessage = ref(0);

const statusMessages = [
    'pages.not_found.status_messages.0',
    'pages.not_found.status_messages.1',
    'pages.not_found.status_messages.2',
    'pages.not_found.status_messages.3',
];

// Cycle through status messages
onMounted(() => {
    setInterval(() => {
        currentMessage.value =
            (currentMessage.value + 1) % statusMessages.length;
    }, 2000);
});
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2
                    class="text-white font-mono text-xl sm:text-2xl tracking-[0.3em] uppercase opacity-90"
                >
                    {{ $t('pages.not_found.title') }}
                </h2>
            </div>
        </template>

        <template #body>
            <div class="min-h-[70vh] flex items-center justify-center p-8">
                <div class="max-w-2xl w-full">
                    <!-- Main 404 Display -->
                    <div class="relative mb-12">
                        <!-- Glitch Effect Container -->
                        <div class="relative inline-block">
                            <h1
                                class="text-[120px] sm:text-[180px] font-mono font-bold text-white/10 select-none leading-none"
                            >
                                {{ glitchText }}
                            </h1>
                            <h1
                                class="absolute top-0 left-0 text-[120px] sm:text-[180px] font-mono font-bold text-white leading-none animate-pulse"
                                style="
                                    text-shadow: 0 0 20px
                                            rgba(255, 255, 255, 0.5),
                                        0 0 40px rgba(255, 255, 255, 0.3);
                                "
                            >
                                {{ glitchText }}
                            </h1>
                        </div>
                    </div>

                    <!-- Status Messages -->
                    <div class="mb-12 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-red-500 animate-pulse"></div>
                            <p
                                class="text-white/60 font-mono text-sm uppercase tracking-[0.3em]"
                            >
                                <span class="text-red-400">{{ $t('pages.not_found.error_label') }}</span>
                                {{ $t(statusMessages[currentMessage]) }}
                            </p>
                        </div>

                        <div class="border-l-2 border-white/10 pl-6 py-4">
                            <p
                                class="text-white/40 font-mono text-xs leading-relaxed"
                            >
                                {{ $t('pages.not_found.description') }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a
                            :href="portfolioUrl('/')"
                            class="group relative px-8 py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-mono text-xs uppercase tracking-[0.3em] transition-all overflow-hidden text-center"
                        >
                            <span
                                class="relative z-10 flex items-center justify-center gap-2"
                            >
                                <span>←</span>
                                <span>{{ $t('pages.not_found.return_home') }}</span>
                            </span>
                            <div
                                class="absolute inset-0 bg-white/5 -translate-x-full group-hover:translate-x-0 transition-transform duration-300"
                            ></div>
                        </a>
                    </div>

                    <!-- System Info Footer -->
                    <div
                        class="mt-16 pt-8 border-t border-white/5 grid grid-cols-2 sm:grid-cols-4 gap-4"
                    >
                        <div>
                            <p
                                class="text-[8px] font-mono text-white/30 uppercase tracking-widest mb-1"
                            >
                                {{ $t('pages.not_found.status') }}
                            </p>
                            <p
                                class="text-[10px] font-mono text-red-400 uppercase"
                            >
                                {{ $t('pages.not_found.error_status') }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-[8px] font-mono text-white/30 uppercase tracking-widest mb-1"
                            >
                                {{ $t('pages.not_found.code') }}
                            </p>
                            <p
                                class="text-[10px] font-mono text-white/60 uppercase"
                            >
                                404
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-[8px] font-mono text-white/30 uppercase tracking-widest mb-1"
                            >
                                {{ $t('pages.not_found.protocol') }}
                            </p>
                            <p
                                class="text-[10px] font-mono text-white/60 uppercase"
                            >
                                HTTP/1.1
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-[8px] font-mono text-white/30 uppercase tracking-widest mb-1"
                            >
                                {{ $t('pages.not_found.timestamp') }}
                            </p>
                            <p
                                class="text-[10px] font-mono text-white/60 uppercase"
                            >
                                {{ new Date().toISOString().split("T")[0] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

<style scoped>
@keyframes glitch {
    0% {
        transform: translate(0);
    }
    20% {
        transform: translate(-2px, 2px);
    }
    40% {
        transform: translate(-2px, -2px);
    }
    60% {
        transform: translate(2px, 2px);
    }
    80% {
        transform: translate(2px, -2px);
    }
    100% {
        transform: translate(0);
    }
}
</style>
