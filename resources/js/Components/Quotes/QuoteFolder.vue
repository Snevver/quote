<script>
import { ref, computed } from 'vue';

export default {
  name: 'QuoteFolder',
  props: { folder: Object, editable: { type: Boolean, default: false }, selectedId: { type: [Number, null], default: null }, asCard: { type: Boolean, default: false }, isMobile: { type: Boolean, default: false } },
  components: { QuoteFolder: () => import('./QuoteFolder.vue') },
  setup(props, { emit }) {
    const isDraggingOver = ref(false);
    const selected = computed(() => props.selectedId === props.folder.id);

    const selectFolder = () => emit('select', props.folder.id);

    const onDrop = (e) => {
      e.preventDefault();
      e.stopPropagation();
      let quoteId = e.dataTransfer.getData('quoteId');
      if (!quoteId && typeof window !== 'undefined' && window.__draggedQuoteId) {
        quoteId = window.__draggedQuoteId;
      }

      const folderId = e.dataTransfer.getData('folderId');

      if (quoteId) {
        emit('drop-quote', { quoteId: Number(quoteId), folderId: props.folder.id });
        if (typeof window !== 'undefined') window.__draggedQuoteId = null;
        return;
      }

      if (folderId) {
        (async () => {
          try {
            const response = await (await import('axios')).default.post(`/api/quote-folders/${Number(folderId)}`, { parent_id: props.folder.id });
            emit('refresh');
          } catch (err) {
            console.error('Failed moving folder:', err.response?.data || err);
          }
        })();
      }
    };

    const onDragStartFolder = (e) => {
      if (!props.editable) return;
      e.dataTransfer.setData('folderId', props.folder.id);
      e.dataTransfer.effectAllowed = 'move';
    };

    const createSubfolder = () => {
      if (!props.editable) return;
      emit('open-create-modal', props.folder.id);
    };

    const onEditFolder = () => {
      if (!props.editable) return;
      emit('edit-folder', props.folder);
    };

    return { selectFolder, onDrop, onDragStartFolder, onEditFolder, createSubfolder, selected, isDraggingOver };
  },
};

</script>

<template>
  <div class="quote-folder">
    <div v-if="asCard"
      class="folder-card p-6 bg-white/5 border border-white/10 rounded flex flex-col items-center justify-center text-center cursor-pointer relative transition-all duration-300 select-none"
      :class="{ 'scale-105 bg-white/10 border-blue-400/50 ring-2 ring-blue-400/20': isDraggingOver }"
      @click.stop.prevent="selectFolder"
      v-bind="!isMobile ? { draggable: editable, onDragstart: onDragStartFolder, onDragover: (e) => e.preventDefault(), onDragenter: (e) => { e.preventDefault(); isDraggingOver = true; }, onDragleave: (e) => { e.preventDefault(); isDraggingOver = false; }, onDrop: onDrop } : {}">
      <svg class="w-12 h-12 text-white/70 mb-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" stroke="currentColor"
          stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <div class="font-mono text-white/80 text-sm uppercase mb-1">{{ folder.name }}</div>
      <div v-if="editable" class="quote-actions">
        <button @click.stop.prevent="onEditFolder" class="action-btn action-edit cursor-pointer"
          :title="$t('common.edit')">
          [{{ $t('common.edit') }}]
        </button>
        <button @click.stop.prevent="$emit('delete-folder', folder)" class="action-btn action-delete cursor-pointer">
          [DELETE]
        </button>
      </div>
      <div class="absolute top-2 right-2 text-[10px] text-white/60 opacity-0 group-hover:opacity-100">
        <div class="font-mono">id: {{ folder.id }}</div>
        <div class="font-mono">p: {{ folder.parent_id ?? 'root' }}</div>
      </div>
    </div>

    <div v-else
      class="folder-node flex items-center justify-between px-2 py-1 rounded hover:bg-white/5 transition-all select-none"
      :class="{ 'bg-white/6': selected, 'bg-blue-400/20 ring-1 ring-blue-400/30': isDraggingOver }"
      @click.stop.prevent="selectFolder"
      v-bind="!isMobile ? { draggable: editable, onDragstart: onDragStartFolder, onDragover: (e) => e.preventDefault(), onDragenter: (e) => { e.preventDefault(); isDraggingOver = true; }, onDragleave: (e) => { e.preventDefault(); isDraggingOver = false; }, onDrop: onDrop } : {}">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-white/70" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" stroke="currentColor"
            stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="text-sm font-mono text-white/80">{{ folder.name }}</span>
      </div>
      <div>
        <button v-if="editable" @click.stop.prevent="onEditFolder"
          class="text-xs px-2 py-0.5 bg-white/10 hover:bg-white/20 rounded cursor-pointer" :title="$t('common.edit')">
          edit
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@reference "tailwindcss";

.folder-node {
  cursor: pointer;
  user-select: none;
}

.folder-node:hover {
  background: rgba(255, 255, 255, 0.02);
}

.folder-node .text-sm {
  user-select: none;
}

.quote-actions {
  @apply flex gap-3 mt-4 pt-4 border-t border-white/10 transition-opacity;
}

.action-btn {
  @apply text-[10px] font-mono uppercase tracking-wider transition-colors;
}

.action-edit {
  @apply text-white/40 hover:text-white;
}

.action-delete {
  @apply text-red-400/60 hover:text-red-400;
}

.dragging {
  transition: none !important;
}

.folder-card {
  user-select: none;
}
</style>
