<script setup>
    import SidebarLogo from "./SidebarLogo.vue";
    import SidebarName from "./SidebarName.vue";
    import SidebarMenu from "./SidebarMenu.vue";
    import SidebarFooter from "./SidebarFooter.vue";
    import LanguageSwitcher from "./LanguageSwitcher.vue";
    import Card from "../Common/Card.vue";
    import { inject } from 'vue';
    import axios from 'axios';
    import { portfolioUrl } from '../../config/domains';

    const isAuthenticated = inject('auth', false);

    const handleLogout = async () => {
        try {
            await axios.post('/logout');
            window.location.href = portfolioUrl('/');
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };
</script>

<template>
    <div class="flex flex-col h-full w-full py-8 relative">
        <!-- Vertical Spine (Backbone) -->
        <div class="absolute left-6 top-10 bottom-20 w-px bg-linear-to-b from-transparent via-white/10 to-transparent pointer-events-none hidden lg:block">
            <!-- Glow points at intersections -->
            <div class="absolute top-[5%] -left-px w-[3px] h-[3px] bg-white rounded-full blur-[1px] opacity-40"></div>
            <div class="absolute top-[20%] -left-px w-[3px] h-[3px] bg-white rounded-full blur-[1px] opacity-40"></div>
            <div class="absolute top-[80%] -left-px w-[3px] h-[3px] bg-white rounded-full blur-[1px] opacity-40"></div>
        </div>

        <!-- Language switcher -->
        <div class="px-6 mb-4 relative group">
            <div class="flex items-center gap-3 mb-2 opacity-30 group-hover:opacity-60 transition-opacity">
                <span class="text-[8px] font-mono text-white tracking-[0.2em] ml-2">[SYS.LOCALE]</span>
            </div>
            <Card :tilt="true" :lines="true" padding="p-3">
                <LanguageSwitcher />
            </Card>
            <!-- Connector to spine -->
            <div class="absolute left-6 top-1/2 w-4 h-px bg-white/10 -translate-y-1/2 pointer-events-none hidden lg:block"></div>
        </div>

        <!-- Identity Section (Logo + Name) -->
        <div class="relative mb-6 mt-4 px-6 group">
            <div class="flex items-center gap-3 mb-4 opacity-30 group-hover:opacity-60 transition-opacity">
                <span class="text-[8px] font-mono text-white tracking-[0.2em] ml-2">[SYS.IDENTITY]</span>
            </div>
            
                <div class="flex flex-col items-center gap-4 relative z-10">
                <SidebarLogo src="/images/sven-image.webp"/>
                <SidebarName :loggedIn="isAuthenticated" />
            </div>

            <!-- Connector to spine -->
            <div class="absolute left-6 top-1/2 w-4 h-px bg-white/10 -translate-y-1/2 pointer-events-none hidden lg:block"></div>
        </div>

        <!-- Menu Section -->
        <div class="grow flex flex-col px-6 relative group">
            <div class="flex items-center gap-3 mb-2 opacity-30 group-hover:opacity-60 transition-opacity">
                <span class="text-[8px] font-mono text-white tracking-[0.2em] ml-2">[NAV.CMD]</span>
            </div>
            <div class="grow">
                <SidebarMenu :auth="isAuthenticated" />
            </div>
            <!-- Connector to spine -->
            <div class="absolute left-6 top-24 w-4 h-px bg-white/10 pointer-events-none hidden lg:block"></div>
        </div>

        <!-- Footer -->
        <div class="mt-auto pt-4 px-6 relative group">
            <div class="flex items-center gap-3 mb-2 opacity-30 group-hover:opacity-60 transition-opacity">
                <span class="text-[8px] font-mono text-white tracking-[0.2em] ml-2">[SYS.INFO]</span>
            </div>
            <SidebarFooter />
            
            <!-- Admin Logout -->
            <div v-if="isAuthenticated" class="mt-4 flex flex-col items-center mb-4">
                <button 
                    @click="handleLogout"
                    class="group/logout flex items-center gap-2 px-4 py-2 bg-red-500/5 hover:bg-red-500/10 border border-red-500/20 hover:border-red-500/40 transition-all active:scale-95 hover:cursor-pointer"
                >
                    <span class="text-[9px] font-mono text-red-500/60 group-hover/logout:text-red-400 tracking-[0.2em] uppercase">
                        {{ $t('menu.logout') }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500/40 group-hover/logout:text-red-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>

            <!-- Connector to spine -->
            <div class="absolute left-6 top-1/2 w-4 h-px bg-white/10 -translate-y-1/2 pointer-events-none hidden lg:block"></div>
        </div>
    </div>
</template>