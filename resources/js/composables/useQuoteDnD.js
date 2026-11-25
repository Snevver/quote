import { ref, watch } from 'vue';
import axios from 'axios';

export function useQuoteDnD(visibleQuotes, selectedFolderId, fetchQuotes, fetchFolders) {
  const dragList = ref([]);
  const movedToFolder = ref(false);

  // Sync dragList with visibleQuotes
  watch(visibleQuotes, (val) => {
    dragList.value = (val || []).map(q => ({ ...q }));
  }, { immediate: true });

  const onDragStart = (evt) => {
    movedToFolder.value = false;
    try {
      if (typeof window !== 'undefined' && evt && evt.item) {
        const id = evt.item.dataset?.id || evt.item.getAttribute('data-id');
        if (id) {
          window.__draggedQuoteId = id;
          if (evt.originalEvent && evt.originalEvent.dataTransfer) {
            evt.originalEvent.dataTransfer.setData('quoteId', id);
            evt.originalEvent.dataTransfer.effectAllowed = 'move';
          }
        }
      }
    } catch (e) {
      console.error('onDragStart Error:', e);
    }
  };

  const onDragEnd = async (evt) => {
    if (movedToFolder.value) {
      if (typeof window !== 'undefined') window.__draggedQuoteId = null;
      return;
    }

    const order = dragList.value.map(q => q.id);
    const folderId = selectedFolderId.value ?? null;
    try {
      await axios.post('/api/quotes/reorder', { order, folder_id: folderId });
      await fetchQuotes();
      await fetchFolders();
    } catch (e) {
      console.error('Failed saving drag order', e);
    } finally {
      if (typeof window !== 'undefined') window.__draggedQuoteId = null;
    }
  };

  const onDropQuote = async ({ quoteId, folderId }) => {
    if (!quoteId) return;
    movedToFolder.value = true;
    try {
      await axios.post(`/api/quotes/${quoteId}`, { folder_id: folderId });
      await fetchQuotes();
      await fetchFolders();
    } catch (e) {
      console.error('Failed moving quote:', e);
    }
  };

  const onDropToBreadcrumb = async (e, targetFolderId) => {
    let quoteId = e.dataTransfer?.getData('quoteId');
    if (!quoteId && typeof window !== 'undefined' && window.__draggedQuoteId) quoteId = window.__draggedQuoteId;
    
    const folderId = e.dataTransfer?.getData('folderId');

    if (quoteId) {
      movedToFolder.value = true;
      try {
        await axios.post(`/api/quotes/${quoteId}`, { folder_id: targetFolderId });
        await fetchQuotes();
        await fetchFolders();
      } catch (err) {
        console.error('Failed moving quote to breadcrumb:', err);
      }
      return;
    }

    if (folderId) {
      try {
        await axios.post(`/api/quote-folders/${Number(folderId)}`, { parent_id: targetFolderId });
        await fetchFolders();
        await fetchQuotes();
      } catch (err) {
        console.error('Failed moving folder to breadcrumb:', err);
      }
    }
  };

  return {
    dragList,
    onDragStart,
    onDragEnd,
    onDropQuote,
    onDropToBreadcrumb
  };
}
