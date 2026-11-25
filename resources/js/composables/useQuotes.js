import { ref } from 'vue';
import axios from 'axios';

export function useQuotes() {
  const localQuotes = ref([]);
  const loading = ref(false);

  const fetchQuotes = async () => {
    loading.value = true;
    try {
      const response = await axios.get("/api/quotes");
      if (response.data.quotes) {
        localQuotes.value = response.data.quotes;
      }
    } catch (error) {
      console.error("Failed to fetch quotes:", error);
    } finally {
      loading.value = false;
    }
  };

  const deleteQuote = async (quoteId) => {
    await axios.delete(`/api/quotes/${quoteId}`);
    localQuotes.value = localQuotes.value.filter(q => q.id !== quoteId);
  };

  return {
    localQuotes,
    loading,
    fetchQuotes,
    deleteQuote
  };
}
