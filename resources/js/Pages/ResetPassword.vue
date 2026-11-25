<script setup>
    import { ref } from "vue";
    import axios from "axios";
    import AppLayout from "../Layouts/AppLayout.vue";

    const props = defineProps({
        token: {
            type: String,
            required: true,
        },
    });

    const password = ref("");
    const passwordConfirmation = ref("");
    const error = ref("");
    const success = ref("");
    const isLoading = ref(false);

    const handleReset = async () => {
        isLoading.value = true;
        error.value = "";
        success.value = "";

        try {
            const response = await axios.post("/inchecken/reset-password", {
                token: props.token,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            });

            success.value = response.data.message || "Password updated.";

            setTimeout(() => {
                window.location.href = response.data.redirect || "/inchecken";
            }, 900);
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                err.response?.data?.errors?.token?.[0] ||
                err.response?.data?.errors?.password?.[0] ||
                "Password reset failed";
        } finally {
            isLoading.value = false;
        }
    };
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="text-white font-mono text-2xl tracking-[0.3em] uppercase opacity-90">[ADMIN.RECOVERY]</h2>
        </template>

        <template #body>
            <div class="min-h-[60vh] flex items-center justify-center p-6">
                <div class="w-full max-w-md bg-black/40 backdrop-blur-xl border border-white/10 p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-2 border-t border-l border-white/40"></div>
                    <div class="absolute top-0 right-0 w-2 h-2 border-t border-r border-white/40"></div>
                    <div class="absolute bottom-0 left-0 w-2 h-2 border-b border-l border-white/40"></div>
                    <div class="absolute bottom-0 right-0 w-2 h-2 border-b border-r border-white/40"></div>

                    <div class="mb-8">
                        <span class="text-[10px] font-mono text-white/30 tracking-[0.4em] uppercase block mb-1">Authorization Required</span>
                        <h3 class="text-white font-sans font-bold text-xl uppercase tracking-tight">Set New Password</h3>
                    </div>

                    <form @submit.prevent="handleReset" class="space-y-6">
                        <div>
                            <label class="block text-[9px] font-mono text-white/50 uppercase tracking-[0.2em] mb-2">New Password</label>
                            <input
                                v-model="password"
                                type="password"
                                required
                                minlength="8"
                                class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white font-mono focus:outline-none focus:border-white/30 transition-colors"
                                placeholder="••••••••"
                            />
                        </div>

                        <div>
                            <label class="block text-[9px] font-mono text-white/50 uppercase tracking-[0.2em] mb-2">Confirm Password</label>
                            <input
                                v-model="passwordConfirmation"
                                type="password"
                                required
                                minlength="8"
                                class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white font-mono focus:outline-none focus:border-white/30 transition-colors"
                                placeholder="••••••••"
                            />
                        </div>

                        <div v-if="error" class="bg-red-500/10 border border-red-500/20 p-3">
                            <p class="text-red-400 text-[10px] font-mono uppercase tracking-wider text-center">{{ error }}</p>
                        </div>

                        <div v-if="success" class="bg-green-500/10 border border-green-500/20 p-3">
                            <p class="text-green-300 text-[10px] font-mono uppercase tracking-wider text-center">{{ success }}</p>
                        </div>

                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-white/10 hover:bg-white/20 border border-white/20 py-4 text-white font-mono text-xs uppercase tracking-[0.3em] transition-all active:scale-[0.98] disabled:opacity-50 hover:cursor-pointer"
                        >
                            {{ isLoading ? "Updating..." : "Reset Password" }}
                        </button>

                        <a
                            href="/inchecken"
                            class="block w-full text-center border border-white/10 py-3 text-white/80 font-mono text-[10px] uppercase tracking-[0.3em] transition-all hover:bg-white/5"
                        >
                            Back to login
                        </a>
                    </form>

                    <div class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center opacity-40">
                        <span class="text-[8px] font-mono text-white tracking-widest uppercase">Encryption: AES-256</span>
                        <span class="text-[8px] font-mono text-white tracking-widest uppercase">Node: NL-SNE-01</span>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>