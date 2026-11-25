import { ref } from 'vue';

/**
 * Composable for managing edit mode state
 * Used across multiple pages for consistent edit mode behavior
 */
export function useEditMode(initialValue = false) {
    const isEditing = ref(initialValue);

    const toggleEdit = () => {
        isEditing.value = !isEditing.value;
    };

    const setEditing = (value) => {
        isEditing.value = value;
    };

    return {
        isEditing,
        toggleEdit,
        setEditing,
    };
}
