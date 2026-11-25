<script setup>
    import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
    import RotatingMenu from './RotatingMenu.vue'
    import Card from '../Common/Card.vue'
    import { useI18n } from "vue-i18n";
    import { getMenuItems } from '../../config/menuItems';
    import axios from 'axios';
    const { t } = useI18n();

    // Initialize from localStorage if available, default to true
    const savedMode = typeof window !== 'undefined' ? localStorage.getItem('sidebar_3d_mode') : null;
    const is3DMode = ref(savedMode !== null ? savedMode === 'true' : true);
    
    const isMobile = ref(false);

    // Persist changes
    watch(is3DMode, (newValue) => {
        localStorage.setItem('sidebar_3d_mode', newValue);
    });

    const checkMobile = () => {
        isMobile.value = window.innerWidth < 1024;
    };

    onMounted(() => {
        checkMobile();
        window.addEventListener('resize', checkMobile);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', checkMobile);
    });

    const props = defineProps({
        auth: Boolean
    });

    const items = computed(() => getMenuItems(t));

    const visibility = ref({});

    // default visible if not set
    const isVisible = (href) => {
        if (visibility.value && Object.prototype.hasOwnProperty.call(visibility.value, href)) {
            return !!visibility.value[href];
        }
        return true;
    };

    // Visible items derived
    const visibleItems = computed(() => items.value.filter(i => isVisible(i.href)));

    const isCompact = computed(() => visibleItems.value.length > 7);
    const listGridClass = computed(() => {
        const n = visibleItems.value.length;
        if (n > 10) return 'grid grid-cols-2 gap-2';
        if (n > 7) return 'grid grid-cols-2 gap-3';
        return 'grid grid-cols-1 gap-3';
    });
</script>

<template>
    <div class="w-full flex flex-col items-center h-full">
        <!-- Menu Content -->
        <div class="w-full flex-1">
            <!-- Rotating menu: desktop only -->
            <RotatingMenu v-if="!isMobile" :items="visibleItems" />
            
            <!-- Static list: mobile only -->
            <div v-if="isMobile" :class="['px-4 mt-2', listGridClass]">
                <div v-for="item in visibleItems" :key="item.href" class="w-full">
                    <a :href="item.href" class="block w-full">
                        <Card :tilt="true" :lines="true" :padding="isCompact ? 'p-2' : 'p-3'" class="w-full group">
                            <div class="flex items-center justify-between px-2">
                                <span :class="['text-white font-medium tracking-widest uppercase transition-all duration-300 group-hover:translate-x-1', isCompact ? 'text-[10px]' : 'text-xs']">
                                    {{ item.title }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white/40 group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </Card>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>