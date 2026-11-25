<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import AppLayout from "../Layouts/AppLayout.vue";
import EditModeButton from "../Components/Common/EditModeButton.vue";
import QuoteCard from "../Components/Quotes/QuoteCard.vue";
import QuoteModal from "../Components/Quotes/QuoteModal.vue";
import QuoteFolder from "../Components/Quotes/QuoteFolder.vue";
import FolderModal from "../Components/Quotes/FolderModal.vue";
import Draggable from 'vuedraggable';
import { useQuoteFolders } from "../composables/useQuoteFolders.js";
import { useQuotes } from "../composables/useQuotes.js";
import { useQuoteExplorer } from "../composables/useQuoteExplorer.js";
import { useQuoteDnD } from "../composables/useQuoteDnD.js";
import { useQuoteModals } from "../composables/useQuoteModals.js";
import AlertModal from "../Components/Common/AlertModal.vue";
import ConfirmModal from "../Components/Common/ConfirmModal.vue";
import EmptyState from "../Components/Common/EmptyState.vue";
import { useI18n } from "vue-i18n";
import { useAlerts } from "../composables/useAlerts.js";
import { useEditMode } from "../composables/useEditMode.js";

const props = defineProps({
    quotes: Array,
    auth: Boolean,
});

const { t } = useI18n();
const {
    alertState,
    showAlert,
    closeAlert,
    confirmState,
    showConfirm,
    closeConfirm,
    handleConfirm,
} = useAlerts();
const { isEditing, toggleEdit, setEditing } = useEditMode();

const {
    localQuotes,
    fetchQuotes,
    deleteQuote
} = useQuotes();

const {
    localFolders,
    fetchFolders,
    deleteFolder
} = useQuoteFolders();

const {
    selectedFolderId,
    selectedTag,
    selectFolder,
    breadcrumbs,
    visibleFolders,
    visibleQuotes
} = useQuoteExplorer(localFolders, localQuotes);

const {
    dragList,
    onDragStart,
    onDragEnd,
    onDropQuote,
    onDropToBreadcrumb
} = useQuoteDnD(visibleQuotes, selectedFolderId, fetchQuotes, fetchFolders);

const {
    showModal,
    showFolderModal,
    editingQuote,
    editingFolder,
    folderModalParent,
    quoteModalFolderId,
    openCreateModal,
    openEditModal,
    openCreateFolder,
    openEditFolderModal,
    closeQuoteModal,
    closeFolderModal
} = useQuoteModals();

const handleSaved = async () => {
    await fetchQuotes();
    showAlert(t("common.success"), t("pages.quotes.quote_saved"), "success");
};

const confirmDeleteQuote = (quote) => {
    showConfirm(
        t("pages.quotes.delete_confirm_title"),
        t("pages.quotes.delete_confirm_message"),
        async () => {
            try {
                await deleteQuote(quote.id);
                showAlert(t("common.success"), t("pages.quotes.quote_deleted"), "success");
            } catch (error) {
                console.error("Failed to delete quote:", error);
                showAlert(t("common.error"), t("common.failed_to_delete"), "error");
            }
        }
    );
};

const confirmDeleteFolder = (folder) => {
    showConfirm(
        `Delete folder: ${folder.name}`,
        `Are you sure you want to delete the folder "${folder.name}"?`,
        async () => {
            try {
                await deleteFolder(folder.id);
                await fetchQuotes();
                showAlert(t('common.success'), `${folder.name} deleted`, 'success');
            } catch (e) {
                console.error('Failed deleting folder', e);
                showAlert(t('common.error'), t('common.failed_to_delete'), 'error');
            }
        }
    );
};

const onFolderCreated = async () => {
    closeFolderModal();
    await fetchFolders();
};

const onFolderUpdated = async () => {
    closeFolderModal();
    await fetchFolders();
    showAlert(t("common.success"), "Folder renamed", "success");
};

// INITIALIZATION
onMounted(() => {
    fetchQuotes();
    fetchFolders();
});

// Mobile detection
const isMobile = ref(false);

// Phone auto-edit mode logic
onMounted(() => {
    if (typeof window !== 'undefined') {
        const mql = window.matchMedia('(max-width: 640px)');
        const applyEditMode = () => {
            isMobile.value = mql.matches;
            if (props.auth && mql.matches) setEditing(true);
        };
        applyEditMode();
        if (mql.addEventListener) mql.addEventListener('change', applyEditMode);
        onUnmounted(() => {
            if (mql.removeEventListener) mql.removeEventListener('change', applyEditMode);
        });
    }
});
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-white font-mono text-xl sm:text-2xl tracking-[0.3em] uppercase opacity-90">
                    [{{ $t("pages.quotes.title") }}]
                </h2>

                <div class="flex gap-4">
                    <EditModeButton v-if="auth" :isEditing="isEditing" @toggle="toggleEdit" />
                </div>
            </div>
        </template>

        <template #body>
            <div class="max-w-7xl mx-auto p-6 md:p-12">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Explorer content (folders + breadcrumbs) -->
                    <section class="col-span-1">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                                <div v-for="(crumb, index) in breadcrumbs" :key="crumb.id ?? 'root'"
                                    class="flex items-center">
                                    <template v-if="index > 0">
                                        <span class="text-white/20 mx-1 font-mono">/</span>
                                    </template>

                                    <button @click="selectFolder(crumb.id)"
                                        v-bind="!isMobile ? {
                                            'onDragover': (e) => e.preventDefault(),
                                            'onDragenter': (e) => { e.preventDefault(); e.currentTarget.classList.add('bg-blue-500/30', 'ring-2', 'ring-blue-400', 'scale-105'); },
                                            'onDragleave': (e) => { e.preventDefault(); e.currentTarget.classList.remove('bg-blue-500/30', 'ring-2', 'ring-blue-400', 'scale-105'); },
                                            'onDrop': (e) => { e.preventDefault(); onDropToBreadcrumb(e, crumb.id); }
                                        } : {}"
                                        class="font-mono text-white/80 text-sm uppercase px-3 py-1 rounded bg-white/5 border border-white/10 transition-all hover:bg-white/10 hover:cursor-pointer whitespace-nowrap"
                                        :class="{ 'border-blue-400/50 bg-white/10': crumb.id === selectedFolderId }"
                                        :title="index === 0 ? 'Root' : crumb.name">
                                        {{ crumb.name }}
                                    </button>
                                </div>
                            </div>
                            <div>
                                <div v-if="auth && isEditing">
                                    <button @click="openCreateFolder(selectedFolderId)"
                                        class="px-2 py-1 bg-white/6 text-xs rounded hover:cursor-pointer">+
                                        folder</button>
                                </div>
                            </div>
                        </div>

                        <!-- Subfolders Grid -->
                        <div v-if="visibleFolders.length > 0"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            <div :id="`folder-drop-${f.id}`" v-for="f in visibleFolders" :key="`folder-${f.id}`"
                                class="folder-drop-target">
                                <QuoteFolder :folder="f" :asCard="true" :editable="isEditing && auth" :isMobile="isMobile"
                                    @drop-quote="onDropQuote" @select="selectFolder" @refresh="fetchFolders"
                                    @open-create-modal="openCreateFolder" @edit-folder="openEditFolderModal"
                                    @delete-folder="confirmDeleteFolder" />
                            </div>
                        </div>

                        <!-- Quotes grid (Draggable) -->
                        <Draggable v-model="dragList" item-key="id" group="quotes" @start="onDragStart" @end="onDragEnd"
                            tag="div" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                            ghost-class="ghost-move" drag-class="drag-active" :animation="250"
                            draggable=".quote-card-item" :disabled="isMobile">
                            <template #item="{ element: quote }">
                                <QuoteCard class="quote-card-item" :key="`quote-${quote.id}`" :quote="quote"
                                    :editable="isEditing" :isMobile="isMobile" @edit="openEditModal" @delete="confirmDeleteQuote" />
                            </template>

                            <template #footer>
                                <div v-if="auth && isEditing" @click="openCreateModal(selectedFolderId)"
                                    class="quote-card group cursor-pointer flex flex-col items-center justify-center text-center">
                                    <div class="text-4xl text-white/60 mb-3">+</div>
                                    <div class="font-mono text-white/80 text-sm uppercase tracking-wider">{{
                                        $t('pages.quotes.add_quote') }}</div>
                                </div>
                            </template>
                        </Draggable>

                        <div v-if="visibleQuotes.length === 0 && visibleFolders.length === 0" class="mt-8">
                            <EmptyState
                                :message="selectedTag ? $t('pages.quotes.no_quotes_tag') : $t('pages.quotes.no_quotes')" />
                        </div>
                    </section>
                </div>
            </div>

            <!-- Modals -->
            <QuoteModal :show="showModal" :quote="editingQuote" :folderId="quoteModalFolderId" @close="closeQuoteModal"
                @saved="handleSaved" />

            <FolderModal :show="showFolderModal" :parentId="folderModalParent" :folder="editingFolder"
                @close="closeFolderModal" @created="onFolderCreated" @updated="onFolderUpdated" />

            <AlertModal :show="alertState.show" :title="alertState.title" :message="alertState.message"
                :type="alertState.type" @close="closeAlert" />

            <ConfirmModal :show="confirmState.show" :title="confirmState.title" :message="confirmState.message"
                @close="closeConfirm" @confirm="handleConfirm" />
        </template>
    </AppLayout>
</template>

<style scoped>
@reference "tailwindcss";

:deep(.ghost-move) {
    opacity: 0.2 !important;
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px dashed rgba(255, 255, 255, 0.2) !important;
}

:deep(.drag-active) {
    opacity: 1 !important;
    transform: scale(1.05) rotate(1deg);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    cursor: grabbing !important;
    z-index: 10000;
    transition: none !important;
}

.folder-drop-target {
    @apply h-full;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>