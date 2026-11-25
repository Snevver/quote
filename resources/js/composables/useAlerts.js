import { reactive } from 'vue';

/**
 * Composable for managing alert and confirmation modals
 * Used across multiple pages for consistent alert/confirm behavior
 */
export function useAlerts() {
    // Alert State
    const alertState = reactive({
        show: false,
        title: '',
        message: '',
        type: 'info',
    });

    const showAlert = (title, message, type = 'info') => {
        alertState.title = title;
        alertState.message = message;
        alertState.type = type;
        alertState.show = true;
    };

    const closeAlert = () => {
        alertState.show = false;
    };

    // Confirm State
    const confirmState = reactive({
        show: false,
        title: '',
        message: '',
        onConfirm: null,
    });

    const showConfirm = (title, message, onConfirm) => {
        confirmState.title = title;
        confirmState.message = message;
        confirmState.onConfirm = onConfirm;
        confirmState.show = true;
    };

    const closeConfirm = () => {
        confirmState.show = false;
        confirmState.onConfirm = null;
    };

    const handleConfirm = () => {
        if (confirmState.onConfirm) {
            confirmState.onConfirm();
        }
    };

    return {
        // Alert
        alertState,
        showAlert,
        closeAlert,
        // Confirm
        confirmState,
        showConfirm,
        closeConfirm,
        handleConfirm,
    };
}
