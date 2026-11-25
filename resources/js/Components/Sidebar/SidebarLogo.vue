<script setup>
    import Image from "../Common/Image.vue";
    import Card from "../Common/Card.vue";
    import { portfolioUrl } from "../../config/domains";

    const props = defineProps({
        src: { type: String, default: "/images/logo.png" }
    });
</script>

<template>
    <div>
        <Card :tilt="true" :lines="true" padding="p-2" rounded="xl">
            <div class="relative group inline-block">
                <div class="overflow-hidden grayscale hover:grayscale-0 transition-all duration-700">
                    <Image
                        :src="props.src"
                        :href="portfolioUrl('/')"
                        padding="p-0"
                        alt="Logo"
                        imgClass="w-32 h-32 object-contain hover:cursor-pointer hover:scale-105 transition-transform duration-200"
                    />
                </div>
            </div>
        </Card>
    </div>
</template>