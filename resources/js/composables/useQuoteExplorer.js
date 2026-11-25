import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

export function useQuoteExplorer(localFolders, localQuotes) {
  const { locale } = useI18n();
  const selectedFolderId = ref(null);
  const selectedTag = ref(null);

  const selectFolder = (id) => {
    selectedFolderId.value = id;
  };

  const goUpOneLevel = () => {
    if (!selectedFolderId.value) return;
    const current = (localFolders.value || []).find(f => f.id === selectedFolderId.value);
    selectedFolderId.value = current?.parent_id ?? null;
  };

  const toggleTag = (tag) => {
    selectedTag.value = selectedTag.value === tag ? null : tag;
  };

  const breadcrumbs = computed(() => {
    const list = [{ id: null, name: 'Root' }];
    if (!selectedFolderId.value) return list;

    const path = [];
    let currentId = selectedFolderId.value;
    while (currentId) {
      const folder = (localFolders.value || []).find(f => f.id === currentId);
      if (folder) {
        path.unshift({ id: folder.id, name: folder.name });
        currentId = folder.parent_id;
      } else {
        break;
      }
    }
    return [...list, ...path];
  });

  const allTags = computed(() => {
    const tags = new Set();
    localQuotes.value.forEach((quote) => {
      const currentTags = locale.value === 'nl' ? quote.tags_nl : quote.tags_en;
      if (currentTags) currentTags.forEach(t => tags.add(t));
    });
    return Array.from(tags).sort();
  });

  const visibleFolders = computed(() => 
    (localFolders.value || [])
      .filter(f => f.parent_id === selectedFolderId.value)
      .sort((a, b) => (a.position || 0) - (b.position || 0))
  );

  const visibleQuotes = computed(() => {
    const folderId = selectedFolderId.value ?? null;
    let list = localQuotes.value.filter(q => (q.folder_id ?? null) === folderId);
    if (selectedTag.value) {
      list = list.filter(q => ((locale.value === 'nl' ? q.tags_nl : q.tags_en) || []).includes(selectedTag.value));
    }
    return list.sort((a, b) => (a.position || 0) - (b.position || 0));
  });

  return {
    selectedFolderId,
    selectedTag,
    selectFolder,
    goUpOneLevel,
    toggleTag,
    breadcrumbs,
    allTags,
    visibleFolders,
    visibleQuotes
  };
}
