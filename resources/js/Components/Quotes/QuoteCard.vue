<script setup>
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    quote: {
        type: Object,
        required: true,
    },
    editable: {
        type: Boolean,
        default: false,
    },
    isMobile: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["edit", "delete"]);
const { locale, t } = useI18n();

const showNote = ref(false);

const onDragStart = (e) => {
    if (!props.editable) return;
    e.dataTransfer.setData('quoteId', props.quote.id);
    e.dataTransfer.effectAllowed = 'move';
    e.currentTarget.classList.add('dragging');
};

const onDragEnd = (e) => {
    e.currentTarget.classList.remove('dragging');
};

const toggleNote = () => {
    showNote.value = !showNote.value;
};

const displayTags = computed(() => {
    const tags =
        locale.value === "nl" ? props.quote.tags_nl : props.quote.tags_en;
    return tags || [];
});

const displayNote = computed(() => {
    const note =
        locale.value === "nl" ? props.quote.note_nl : props.quote.note_en;
    return note;
});
</script>

<template>
    <div class="quote-card group select-none" :draggable="editable && !isMobile"
        v-bind="editable && !isMobile ? { onDragstart: onDragStart, onDragend: onDragEnd } : {}" :data-id="quote.id">
        <!-- Quote Text -->
        <div class="quote-text-wrapper">
            <div class="quote-mark">"</div>
            <p class="quote-text">
                {{ quote.quote }}
            </p>
            <div class="quote-mark-end">"</div>
        </div>

        <!-- Author -->
        <div class="quote-author">— {{ quote.author }}</div>

        <!-- Tags -->
        <div v-if="displayTags && displayTags.length > 0" class="quote-tags">
            <span v-for="(tag, index) in displayTags" :key="index" class="quote-tag cursor-pointer">
                {{ tag }}
            </span>
        </div>

        <!-- Note Toggle -->
        <div v-if="displayNote" class="note-toggle-wrapper">
            <button @click="toggleNote" class="note-toggle cursor-pointer">
                <span class="text-xs">{{ showNote ? "[+]" : "[-]" }}</span>
                <span class="ml-2">{{
                    showNote
                        ? t("pages.quotes.hide_note")
                        : t("pages.quotes.show_note")
                }}</span>
            </button>

            <div v-if="showNote" class="quote-note">
                {{ displayNote }}
            </div>
        </div>

        <!-- Edit/Delete Buttons -->
        <div v-if="editable" :class="['quote-actions', editable ? 'opacity-100' : 'opacity-0 group-hover:opacity-100']">
            <button @click="emit('edit', quote)" class="action-btn action-edit cursor-pointer">
                [EDIT]
            </button>
            <button @click="emit('delete', quote)" class="action-btn action-delete cursor-pointer">
                [DELETE]
            </button>
        </div>

        <!-- Decorative corner -->
        <div class="quote-corner"></div>
    </div>
</template>

<style scoped>
@reference "tailwindcss";

.quote-card {
    @apply relative bg-white/5 border border-white/10 p-6 transition-all duration-300;
    min-height: 200px;
    cursor: grab;
    user-select: none;
}

.quote-card:active {
    cursor: grabbing;
}

.quote-card:hover {
    @apply bg-white/10 border-white/20;
    transform: translateY(-2px);
}

.quote-card.dragging {
    opacity: 0;
}

.quote-text-wrapper {
    @apply relative mb-4;
}

.quote-mark {
    @apply absolute -top-2 -left-2 text-6xl text-white/50 font-serif leading-none;
}

.quote-mark-end {
    @apply absolute -bottom-6 -right-2 text-6xl text-white/50 font-serif leading-none;
}

.quote-text {
    @apply text-white/90 font-mono text-sm md:text-base leading-relaxed italic pl-6 pr-6;
}

.quote-author {
    @apply text-white/60 font-mono text-xs uppercase tracking-widest mt-4 text-right;
}

.quote-tags {
    @apply flex flex-wrap gap-2 mt-4 pt-4 border-t border-white/10;
}

.quote-tag {
    @apply px-2 py-1 text-[10px] font-mono uppercase tracking-wider border border-white/20 text-white/60;
    @apply hover:border-white/40 hover:text-white/80 transition-colors;
}

.note-toggle-wrapper {
    @apply mt-4 pt-4 border-t border-white/10;
}

.note-toggle {
    @apply text-white/40 hover:text-white font-mono text-xs uppercase tracking-wider transition-colors;
}

.quote-note {
    @apply mt-3 p-3 bg-white/5 border-l-2 border-white/20 text-white/70 font-mono text-xs leading-relaxed;
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

.quote-corner {
    @apply absolute bottom-0 right-0 w-6 h-6 opacity-10 transition-opacity duration-300;
    clip-path: polygon(100% 0, 100% 100%, 0 100%);
    background: linear-gradient(135deg, transparent 50%, #ffffff 50%);
}

.quote-card:hover .quote-corner {
    opacity: 0.3;
}
</style>
