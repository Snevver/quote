<script setup>
    import { ref, onMounted } from 'vue';
    import Sidebar from '../Components/Sidebar/Sidebar.vue';
    import BinaryBackground from '../Components/Backgrounds/BinaryBackground.vue';

    const isSidebarOpen = ref(false);

    const showWinners = ref(false);

    const toggleSidebar = () => {
        isSidebarOpen.value = !isSidebarOpen.value;
    };
</script>

<template>
    <div class="min-h-screen bg-black relative">
        <!-- Global Binary Background -->
        <div class="fixed inset-0 z-0">
            <BinaryBackground :fps="12" :density="0.2" />
        </div>


        <!-- Sidebar overlay (Mobile) -->
        <div 
            v-if="isSidebarOpen" 
            @click="toggleSidebar" 
            class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 transition-opacity duration-300 cursor-pointer"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'text-white w-80 h-screen fixed left-0 top-0 flex flex-col items-center z-60 transition-transform duration-300 ease-in-out',
                'overflow-hidden border-r border-white/20 shadow-[4px_0_24px_rgba(255,255,255,0.05)]',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
        >
            <!-- Sidebar HUD effect layers -->
            <div class="absolute inset-0 z-0">
                <!-- Deep solid base -->
                <div class="absolute inset-0 bg-black/80"></div>
                
                <!-- Technical grid texture -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" 
                    style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;">
                </div>
                
                <!-- Linear scanlines (vertical) -->
                <div class="absolute inset-0 opacity-[0.02] pointer-events-none bg-[linear-gradient(90deg,white_1px,transparent_1px)] bg-size-[40px_100%]"></div>
                
                <!-- Digital vignette -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.6)_100%)]"></div>
            </div>
            
            <!-- Mobile close button inside sidebar -->
            <button 
                @click="toggleSidebar" 
                class="lg:hidden absolute top-4 right-4 text-white p-2 z-70 hover:bg-white/10 rounded-full transition-colors cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="relative z-10 w-full h-full flex flex-col overflow-y-auto scrollbar-hide touch-pan-y">
                <Sidebar />
            </div>
        </aside>

        <!-- Main content -->
        <main 
            :class="[
                'min-h-screen transition-all duration-300 relative z-10',
                'lg:ml-80'
            ]"
        >
            <div class="relative min-h-screen flex flex-col">
                <!-- Page header (fixed to viewport) -->
                <header v-if="$slots.header" class="fixed top-0 left-0 right-0 z-50 px-6 py-4 lg:py-8 border-b border-white/10 bg-black/60 backdrop-blur-md">
                    <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 lg:pl-80 page-header flex-nowrap">
                        <div class="flex items-center gap-4 flex-1 min-w-0 overflow-hidden">
                            <!-- HUD accent -->
                            <div class="w-1 h-8 bg-white/80 shadow-[0_0_15px_rgba(255,255,255,0.5)] flex-shrink-0"></div>
                            <div class="min-w-0 w-full truncate overflow-hidden">
                                <slot name="header" />
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Mobile menu button -->
                            <button 
                                v-if="!isSidebarOpen" 
                                @click="toggleSidebar" 
                                class="sm:hidden bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 rounded-sm transition-all active:scale-95 hover:cursor-pointer"
                            >
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M12 12h8m-8 6h8" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Page body (pad top to account for fixed header) -->
                <div :class="['grow relative', $slots.header ? 'pt-20 sm:pt-24 lg:pt-24' : '']">
                    <slot name="body" />
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Reduce header title sizes on small screens to improve mobile layout */
:deep(.page-header h2) {
    font-size: 1rem !important; /* ~text-base on mobile */
    line-height: 1.1 !important;
}
@media (min-width: 640px) {
    :deep(.page-header h2) {
        font-size: 1.25rem !important; /* ~text-xl on small screens and up */
    }
}
@media (min-width: 1024px) {
    :deep(.page-header h2) {
        font-size: 1.5rem !important; /* ~text-2xl on large screens */
    }
}
</style>