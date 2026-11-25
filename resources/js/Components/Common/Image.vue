<script setup>
    const props = defineProps({
        src: { type: String, default: "/images/logo.png" },
        alt: { type: String, default: "Logo" },
        imgClass: { type: String, default: "" },
        padding: { type: String, default: "p-4" },
        href: { type: String, default: "/" },
        target: { type: String, default: "_self" },
    });
</script>

<template>
    <div :class="props.padding">
        <a :href="href" :target="target" rel="noopener noreferrer">
            <img :src="src" :alt="alt" :class="imgClass" />
        </a>
    </div>
</template>