<script setup>
    import { useI18n } from "vue-i18n";
    const year = new Date().getFullYear();
    const { t } = useI18n();
</script>

<template>
    <footer class="w-full py-4 text-center bg-transparent mt-4 opacity-40 hover:opacity-100 transition-opacity">
        <p class="text-[9px] font-mono text-white/60 tracking-tighter leading-relaxed">
            [SYS.CPY] &copy; {{ year }}<br />
            [USR.ID] Sven Hoeksema<br />
            [INFO] {{ $t('footer.all_rights_reserved') }}
        </p>
    </footer>
</template>