<script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
    import Card from '../Common/Card.vue';

    const props = defineProps({
        items: { type: Array, required: true }
    });

    const rotation = ref(0);
    const isDragging = ref(false);
    const isHovered = ref(false);
    const startY = ref(0);
    const startRotation = ref(0);
    let autoSpinRaf = null;

    // Dynamic sizing for 3D geometry (shrink when many items).
    // Capped independently of container height so cards stay a sane size
    // on tall/large-screen sidebars instead of ballooning to fill the space.
    const itemHeight = computed(() => {
        const n = props.items.length || 1;
        // If we measured available height, pick a preferred card height relative to it
        if (measuredHeight.value) {
            const base = Math.max(56, Math.min(160, Math.floor(measuredHeight.value * 0.22)));
            // scale down when there are many items
            if (n <= 6) return base;
            if (n <= 10) return Math.floor(base * 0.85);
            if (n <= 16) return Math.floor(base * 0.7);
            return Math.floor(base * 0.56);
        }

        // fallback static sizes
        if (n <= 6) return 140;
        if (n <= 10) return 110;
        if (n <= 16) return 88;
        return 72;
    });

    const radius = computed(() => {
        const count = props.items.length || 1;
        return (itemHeight.value / 2) / Math.tan(Math.PI / count);
    });

    // Measure available space on the parent container and allow a larger max so cards can grow
    const root = ref(null);
    const measuredHeight = ref(0);
    let resizeObserver = null;

    const measure = () => {
        const el = root.value?.parentElement || root.value;
        measuredHeight.value = el ? el.clientHeight : 0;
    };

    onMounted(() => {
        measure();
        const el = root.value?.parentElement || root.value;
        if (el && typeof ResizeObserver !== 'undefined') {
            resizeObserver = new ResizeObserver(measure);
            resizeObserver.observe(el);
        } else {
            window.addEventListener('resize', measure);
        }
    });

    onBeforeUnmount(() => {
        if (resizeObserver) resizeObserver.disconnect();
        else window.removeEventListener('resize', measure);
    });

    const sceneHeight = computed(() => {
        const preferred = itemHeight.value * 2;
        const availableMax = measuredHeight.value ? Math.max(360, measuredHeight.value - 40) : 520;
        return Math.max(120, Math.min(availableMax, preferred));
    });

    const containerHeight = computed(() => measuredHeight.value ? Math.max(180, measuredHeight.value) : Math.max(180, sceneHeight.value + 40));

    const handleWheel = (e) => {
        rotation.value -= e.deltaY * 0.1;
    };

    const rotateBy = (steps) => {
        const angleStep = 360 / props.items.length;
        rotation.value += steps * angleStep;
    };

    const handleMouseDown = (e) => {
        isDragging.value = true;
        startY.value = e.clientY;
        startRotation.value = rotation.value;
        window.addEventListener('mousemove', handleMouseMove);
        window.addEventListener('mouseup', handleMouseUp);
    };

    const handleMouseMove = (e) => {
        if (!isDragging.value) return;
        const deltaY = e.clientY - startY.value;
        rotation.value = startRotation.value + deltaY * 0.5;
    };

    const handleMouseUp = () => {
        isDragging.value = false;
        window.removeEventListener('mousemove', handleMouseMove);
        window.removeEventListener('mouseup', handleMouseUp);
        
        // Snap to nearest item
        const angleStep = 360 / props.items.length;
        rotation.value = Math.round(rotation.value / angleStep) * angleStep;
    };

    // Touch support
    const handleTouchStart = (e) => {
        isDragging.value = true;
        startY.value = e.touches[0].clientY;
        startRotation.value = rotation.value;
    };

    const handleTouchMove = (e) => {
        if (!isDragging.value) return;
        const deltaY = e.touches[0].clientY - startY.value;
        rotation.value = startRotation.value + deltaY * 0.5;
    };

    const handleTouchEnd = () => {
        handleMouseUp();
    };

    const getItemStyle = (index) => {
        const angleStep = 360 / props.items.length;
        const angle = index * angleStep;
        
        // Always visible, no opacity fading
        return {
            // center vertically first, then rotate around X and push outwards
            transform: `translateY(-50%) rotateX(${angle}deg) translateZ(${radius.value}px)`,
            opacity: 1,
            height: `${itemHeight.value}px`
        };
    };

    // A larger radius (bigger cards, or fewer of them) sweeps more screen
    // distance per degree of rotation. Scale the angular step inversely so
    // the wheel's apparent speed stays constant regardless of radius.
    const REFERENCE_RADIUS = 120;
    const AUTO_SPIN_DEG_PER_FRAME = 0.1;

    const animate = () => {
        if (!isDragging.value && !isHovered.value) {
            const speedScale = radius.value > 0 ? REFERENCE_RADIUS / radius.value : 1;
            rotation.value += AUTO_SPIN_DEG_PER_FRAME * speedScale;
        }
        autoSpinRaf = requestAnimationFrame(animate);
    };

    onMounted(() => {
        autoSpinRaf = requestAnimationFrame(animate);
    });

    onBeforeUnmount(() => {
        if (autoSpinRaf) cancelAnimationFrame(autoSpinRaf);
    });
</script>

<template>
    <div 
        class="menu-container group/container"
        ref="root"
        @wheel.prevent="handleWheel"
        @mousedown="handleMouseDown"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        :style="{ minHeight: `${containerHeight}px`, height: '100%' }"
    >
        <!-- Navigation Arrows -->
        <button 
            @click="rotateBy(1)"
            class="absolute top-2 left-1/2 -translate-x-1/2 z-30 p-2 text-white/20 hover:text-white transition-all duration-300 opacity-0 group-hover/container:opacity-100 hover:cursor-pointer"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>

        <button 
            @click="rotateBy(-1)"
            class="absolute bottom-2 left-1/2 -translate-x-1/2 z-30 p-2 text-white/20 hover:text-white transition-all duration-300 opacity-0 group-hover/container:opacity-100 hover:cursor-pointer"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div class="scene" :style="{ minHeight: `${sceneHeight}px`, height: '100%' }">
            <div 
                class="carousel" 
                :style="{ transform: `rotateX(${-rotation}deg)` }"
            >
                <!-- Vertical Spine -->
                <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
                    <div class="w-[2px] h-[200%] bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
                </div>

                <div 
                    v-for="(item, index) in items" 
                    :key="index"
                    class="carousel-item"
                    :style="getItemStyle(index)"
                >
                    <!-- Horizontal Connector to Spine -->
                    <div class="absolute left-1/2 -translate-x-1/2 w-[2px] h-full flex items-center justify-center pointer-events-none">
                        <div 
                            class="w-[40px] h-[1px] bg-white/20"
                            :style="{ transform: `translateZ(${-radius/2}px)` }"
                        ></div>
                    </div>

                    <a :href="item.href" class="block w-full h-full" @click.stop>
                        <Card 
                            :tilt="false" 
                            :lines="true" 
                            padding="p-0" 
                            class="w-full h-full pointer-events-auto card-override shadow-[0_0_40px_rgba(255,255,255,0.15)]"
                        >
                            <div class="flex items-center justify-center w-full h-full text-center">
                                <span class="text-white font-bold tracking-widest uppercase text-[10px] sm:text-xs px-4">
                                    {{ item.title }}
                                </span>
                            </div>
                        </Card>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .menu-container {
        position: relative;
        width: 100%;
        display: flex;
        flex: 1 1 auto;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: grab;
        user-select: none;
        margin-top: 0;
    }

    .menu-container:active {
        cursor: grabbing;
    }

    .scene {
        height: 100%;
        width: 220px;
        perspective: 1000px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .carousel {
        width: 100%;
        height: 100%;
        position: absolute;
        transform-style: preserve-3d;
        transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .carousel-item {
        position: absolute;
        width: 100%;
        left: 0;
        top: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform-origin: center center;
        /* Removed backface-visibility: hidden to prevent disappearing cards */
        transition: opacity 0.3s ease;
    }

    /* Force card styling */
    /* Force card styling with White Accents */
    :deep(.card-override .card-inner > div:first-child) {
        /* Rich dark gradient base */
        background: linear-gradient(180deg, #1a1a1a 0%, #000000 100%) !important;
        
        /* Subtle white border */
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        
        /* White top accent (inset highlight) + soft shadow */
        box-shadow: 
            inset 0 1px 0 0 rgba(255, 255, 255, 0.3),
            0 4px 20px rgba(0, 0, 0, 0.5) !important;
            
        transition: all 0.3s ease !important;
    }

    :deep(.card-override:hover .card-inner > div:first-child) {
        /* Lighter gradient on hover */
        background: linear-gradient(180deg, #2a2a2a 0%, #0a0a0a 100%) !important;
        
        /* Stronger white border */
        border-color: rgba(255, 255, 255, 0.4) !important;
        
        /* Stronger top accent and outer glow */
        box-shadow: 
            inset 0 1px 0 0 rgba(255, 255, 255, 0.6),
            0 0 25px rgba(255, 255, 255, 0.15) !important;
    }
</style>