import { ref, onMounted } from 'vue';
import axios from 'axios';

export function useQuoteFolders(initial = []) {
    const localFolders = ref(initial || []);

    const fetchFolders = async () => {
        try {
            const response = await axios.get('/api/quotes');
            if (response.data?.folders) localFolders.value = response.data.folders;
        } catch (e) {
            console.error('Failed to fetch folders:', e);
        }
    };

    const createFolder = async (payload) => {
        const res = await axios.post('/api/quote-folders', payload);
        if (res.data?.folder) await fetchFolders();
        return res.data;
    };

    const updateFolder = async (folderId, payload) => {
        const res = await axios.post(`/api/quote-folders/${folderId}`, payload);
        if (res.data?.folder) await fetchFolders();
        return res.data;
    };

    const deleteFolder = async (folderId) => {
        const res = await axios.delete(`/api/quote-folders/${folderId}`);
        await fetchFolders();
        return res.data;
    };

    onMounted(fetchFolders);

    return {
        localFolders,
        fetchFolders,
        createFolder,
        updateFolder,
        deleteFolder,
    };
}
