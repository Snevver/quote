<script setup>
    import { ref } from "vue";
    import axios from "axios";
    import AppLayout from "../Layouts/AppLayout.vue";

    const password = ref("");
    const error = ref("");
    const resetMessage = ref("");
    const isLoading = ref(false);
    const isResetting = ref(false);

    const handleLogin = async () => {
        isLoading.value = true;
        error.value = "";
        try {
            const response = await axios.post("/login", {
                password: password.value,
            });
            window.location.href = response.data.redirect || "/";
        } catch (err) {
            error.value =
                err.response?.data?.errors?.password || "Authentication failed";
        } finally {
            isLoading.value = false;
        }
    };

    const handlePasswordResetRequest = async () => {
        isResetting.value = true;
        error.value = "";
        resetMessage.value = "";

        try {
            const response = await axios.post("/inchecken/reset-password/request");

            resetMessage.value = response.data.message || "Reset email sent.";
        } catch (err) {
            error.value =
                err.response?.data?.message || "Password reset request failed";
        } finally {
            isResetting.value = false;
        }
    };
</script>

<template>
    <AppLayout>
        <template #header>
            <h2
                class="text-white font-mono text-2xl tracking-[0.3em] uppercase opacity-90"
            >
                [ADMIN.ACCESS]
            </h2>
        </template>

        <template #body>
            <div class="min-h-[60vh] flex items-center justify-center p-6">
                <div
                    class="w-full max-w-md bg-black/40 backdrop-blur-xl border border-white/10 p-8 relative overflow-hidden"
                >
                    <!-- HUD Decorative Elements -->
                    <div
                        class="absolute top-0 left-0 w-2 h-2 border-t border-l border-white/40"
                    ></div>
                    <div
                        class="absolute top-0 right-0 w-2 h-2 border-t border-r border-white/40"
                    ></div>
                    <div
                        class="absolute bottom-0 left-0 w-2 h-2 border-b border-l border-white/40"
                    ></div>
                    <div
                        class="absolute bottom-0 right-0 w-2 h-2 border-b border-r border-white/40"
                    ></div>

                    <div class="mb-8">
                        <span
                            class="text-[10px] font-mono text-white/30 tracking-[0.4em] uppercase block mb-1"
                            >Authorization Required</span
                        >
                        <h3
                            class="text-white font-sans font-bold text-xl uppercase tracking-tight"
                        >
                            System Login
                        </h3>
                    </div>

                    <form @submit.prevent="handleLogin" class="space-y-6">
                        <div>
                            <label
                                class="block text-[9px] font-mono text-white/50 uppercase tracking-[0.2em] mb-2"
                                >Access Key</label
                            >
                            <input
                                v-model="password"
                                type="password"
                                required
                                class="w-full bg-white/5 border border-white/10 px-4 py-3 text-white font-mono focus:outline-none focus:border-white/30 transition-colors"
                                placeholder="••••••••"
                            />
                        </div>

                        <div
                            v-if="error"
                            class="bg-red-500/10 border border-red-500/20 p-3"
                        >
                            <p
                                class="text-red-400 text-[10px] font-mono uppercase tracking-wider text-center"
                            >
                                {{ error }}
                            </p>
                        </div>

                        <div
                            v-if="resetMessage"
                            class="bg-green-500/10 border border-green-500/20 p-3"
                        >
                            <p
                                class="text-green-300 text-[10px] font-mono uppercase tracking-wider text-center"
                            >
                                {{ resetMessage }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-white/10 hover:bg-white/20 border border-white/20 py-4 text-white font-mono text-xs uppercase tracking-[0.3em] transition-all active:scale-[0.98] disabled:opacity-50 hover:cursor-pointer"
                        >
                            {{ isLoading ? "Processing..." : "Execute Login" }}
                        </button>

                        <button
                            type="button"
                            @click="handlePasswordResetRequest"
                            :disabled="isResetting"
                            class="w-full border border-white/10 py-3 text-white/80 font-mono text-[10px] uppercase tracking-[0.3em] transition-all hover:bg-white/5 disabled:opacity-50 hover:cursor-pointer"
                        >
                            {{ isResetting ? "Sending..." : "Reset Password" }}
                        </button>
                    </form>

                    <div
                        class="mt-8 pt-6 border-t border-white/5 flex justify-between items-center opacity-40"
                    >
                        <span
                            class="text-[8px] font-mono text-white tracking-widest uppercase"
                            >Encryption: AES-256</span
                        >
                        <span
                            class="text-[8px] font-mono text-white tracking-widest uppercase"
                            >Node: NL-SNE-01</span
                        >
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>