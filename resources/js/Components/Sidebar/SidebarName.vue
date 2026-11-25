<script setup>
    import Card from "../Common/Card.vue";
    import { useI18n } from "vue-i18n";

    const props = defineProps({
        loggedIn: { type: Boolean, default: false },
    });

    const name = "Sven Hoeksema";
</script>

<template>
    <div>
        <a v-if="loggedIn" href="/" class="group block hover:cursor-pointer">
            <Card :tilt="true" :lines="true" padding="p-3" class="text-center">
                <h1 class="text-xl font-sans font-semibold text-white tracking-wider px-4">
                    {{ name }}
                </h1>
            </Card>
        </a>

        <Card v-else :tilt="true" :lines="true" padding="p-3" class="text-center">
            <h1 class="text-xl font-sans font-semibold text-white tracking-wider px-4">
                {{ name }}
            </h1>
        </Card>
    </div>
</template>