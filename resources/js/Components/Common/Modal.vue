<script setup>
    import { onMounted, onUnmounted } from "vue";

    const props = defineProps({
        show: Boolean,
        maxWidth: {
            type: String,
            default: "2xl",
        },
        closeable: {
            type: Boolean,
            default: true,
        },
        zIndex: {
            type: Number,
            default: 100,
        },
    });

    const emit = defineEmits(["close"]);

    const close = () => {
        if (props.closeable) {
            emit("close");
        }
    };

    const closeOnEscape = (e) => {
        if (e.key === "Escape" && props.show) {
            close();
        }
    };

    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));

    const maxWidthClass = {
        sm: "sm:max-w-sm",
        md: "sm:max-w-md",
        lg: "sm:max-w-lg",
        xl: "sm:max-w-xl",
        "2xl": "sm:max-w-2xl",
        "3xl": "sm:max-w-3xl",
        "4xl": "sm:max-w-4xl",
        "5xl": "sm:max-w-5xl",
        full: "sm:max-w-full",
    }[props.maxWidth];
</script>

<template>
    <Teleport to="body">
        <transition
            leave-active-class="duration-200"
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="show"
                class="fixed inset-y-0 right-0 lg:left-80 left-0 overflow-y-auto px-4 py-6 sm:px-0 flex items-start justify-center"
                :style="{ zIndex: props.zIndex }"
            >
                <!-- Overlay -->
                <div
                    v-show="show"
                    class="fixed inset-y-0 right-0 lg:left-80 left-0 transform transition-all"
                    @click="close"
                >
                    <div
                        class="absolute inset-0 bg-black/80 backdrop-blur-sm"
                    ></div>
                </div>

                <!-- Modal Content -->
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-show="show"
                        :class="[
                            'bg-black/90 border border-white/10 shadow-2xl transform transition-all w-full relative overflow-hidden',
                            maxWidthClass,
                        ]"
                    >
                        <!-- HUD Decorative Elements -->
                        <div
                            class="absolute top-0 left-0 w-4 h-4 border-t border-l border-white/40"
                        ></div>
                        <div
                            class="absolute top-0 right-0 w-4 h-4 border-t border-r border-white/40"
                        ></div>
                        <div
                            class="absolute bottom-0 left-0 w-4 h-4 border-b border-l border-white/40"
                        ></div>
                        <div
                            class="absolute bottom-0 right-0 w-4 h-4 border-b border-r border-white/40"
                        ></div>

                        <!-- Close Button -->
                        <button
                            @click="close"
                            class="absolute top-4 right-4 text-white/40 hover:text-white transition-colors z-50 hover:cursor-pointer"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>

                        <div class="p-8">
                            <slot />
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </Teleport>
</template>