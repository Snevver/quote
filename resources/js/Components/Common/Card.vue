<script setup>
    import { ref, onMounted, onBeforeUnmount } from "vue";

    const props = defineProps({
        tilt: { type: Boolean, default: true },
        glow: { type: Boolean, default: true },
        lines: { type: Boolean, default: true },
        padding: { type: String, default: "p-4" },
        rounded: { type: String, default: "xl" }, // xl, full, etc
        class: { type: String, default: "" },
    });

    const cardRef = ref(null);
    const tiltX = ref(0);
    const tiltY = ref(0);
    const isHovered = ref(false);

    const handleMouseMove = (e) => {
        if (!props.tilt || !cardRef.value) return;

        const rect = cardRef.value.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = ((y - centerY) / centerY) * -10; // Max 10 degrees
        const rotateY = ((x - centerX) / centerX) * 10; // Max 10 degrees

        tiltX.value = rotateX;
        tiltY.value = rotateY;
    };

    const handleMouseLeave = () => {
        tiltX.value = 0;
        tiltY.value = 0;
        isHovered.value = false;
    };

    const handleMouseEnter = () => {
        isHovered.value = true;
    };
</script>

<template>
    <div
        ref="cardRef"
        class="card-wrapper relative group"
        :class="[props.class]"
        @mousemove="handleMouseMove"
        @mouseleave="handleMouseLeave"
        @mouseenter="handleMouseEnter"
        :style="{
            perspective: '1000px',
        }"
    >
        <div
            class="card-inner relative transition-transform duration-200 ease-out"
            :style="{
                transform: `rotateX(${tiltX}deg) rotateY(${tiltY}deg)`,
                transformStyle: 'preserve-3d',
            }"
        >
            <!-- Background / Glassmorphism -->
            <div
                class="absolute inset-0 bg-white/5 backdrop-blur-md border border-white/10 shadow-2xl transition-all duration-300 group-hover:bg-white/10 group-hover:border-white/20"
                :class="[
                    props.glow && isHovered ? 'glow-effect' : '',
                    props.rounded === 'full' ? 'rounded-full' : 'rounded-xl'
                ]"
            ></div>

            <!-- Animated Lines (Borders) -->
            <div v-if="props.lines" class="absolute inset-0 pointer-events-none">
                <svg class="w-full h-full overflow-visible" preserveAspectRatio="none">
                    <rect
                        x="0" y="0"
                        width="100%" height="100%"
                        :rx="props.rounded === 'full' ? '9999px' : '12px'"
                        fill="none"
                        stroke="rgba(255,255,255,0.6)"
                        stroke-width="1.5"
                        pathLength="100"
                        class="shining-path"
                    />
                </svg>
            </div>

            <div :class="[props.padding, 'relative z-10 h-full overflow-hidden flex flex-col']">
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
    .card-wrapper {
        display: block;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .card-inner {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .glow-effect {
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.1), 
                    inset 0 0 15px rgba(255, 255, 255, 0.05);
    }

    /* Animated Lines */
    .shining-path {
        stroke-dasharray: 25 75;
        stroke-dashoffset: 100;
        transition: opacity 0.3s ease;
        opacity: 0;
        animation: shine 3s infinite linear;
    }

    .group:hover .shining-path {
        opacity: 1;
    }

    @keyframes shine {
        from {
            stroke-dashoffset: 100;
        }
        to {
            stroke-dashoffset: 0;
        }
    }
</style>