import { ref } from 'vue';

export function useQuoteModals() {
  const showModal = ref(false);
  const showFolderModal = ref(false);
  const editingQuote = ref(null);
  const editingFolder = ref(null);
  const folderModalParent = ref(null);
  const quoteModalFolderId = ref(null);

  const openCreateModal = (folderId = null) => {
    quoteModalFolderId.value = folderId;
    editingQuote.value = null;
    showModal.value = true;
  };

  const openEditModal = (quote) => {
    editingQuote.value = quote;
    showModal.value = true;
  };

  const openCreateFolder = (parentId = null) => {
    folderModalParent.value = parentId;
    editingFolder.value = null;
    showFolderModal.value = true;
  };

  const openEditFolderModal = (folder) => {
    editingFolder.value = folder;
    showFolderModal.value = true;
  };

  const closeQuoteModal = () => {
    showModal.value = false;
    editingQuote.value = null;
    quoteModalFolderId.value = null;
  };

  const closeFolderModal = () => {
    showFolderModal.value = false;
    editingFolder.value = null;
    folderModalParent.value = null;
  };

  return {
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
  };
}
