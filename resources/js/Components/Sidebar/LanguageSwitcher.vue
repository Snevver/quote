<script setup>
    import { useI18n } from "vue-i18n";
    import { computed } from "vue";
    const { locale } = useI18n();
    const current = computed(() => locale.value);
    const setLocale = (l) => {
        locale.value = l;
        localStorage.setItem('locale', l);
    };
</script>

<template>
    <div class="flex w-full items-center justify-center gap-3 my-auto">
        <button
            class="lang-btn"
            @click="setLocale('en')"
            :aria-pressed="current === 'en'"
            :data-active="current === 'en'"
        >
            EN
        </button>
        <span class="w-px h-4 bg-white/20 rounded-full block shrink-0"></span>
        <button
            class="lang-btn"
            @click="setLocale('nl')"
            :aria-pressed="current === 'nl'"
            :data-active="current === 'nl'"
        >
            NL
        </button>
    </div>
</template>

<style scoped>
    .lang-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 34px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: rgba(229, 231, 235, 0.45);
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        cursor: pointer;
        transition: color 0.2s ease, border-color 0.2s ease, background 0.2s ease, transform 0.15s ease;
    }

    .lang-btn:hover {
        color: #e5e7eb;
        border-color: rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.06);
        transform: scale(1.05);
    }

    .lang-btn[data-active="true"] {
        color: #f9fafb;
        border-color: rgba(255, 255, 255, 0.35);
        background: rgba(255, 255, 255, 0.09);
    }
</style>
