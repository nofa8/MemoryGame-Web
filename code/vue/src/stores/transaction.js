import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'

export const useTransactionStore = defineStore('transaction', () => {
  const transactions = ref([])
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const perPage = ref(10)

  const totalTransactions = computed(() => transactions.value.length)

  const storeError = useErrorStore()

  const fetchTransactions = async (page = 1, filters = {}) => {
    try {
        const params = { page, ...filters };

        const response = await axios.get('transactions', { params });

        transactions.value = response.data.data;
        currentPage.value = response.data.meta.current_page;
        lastPage.value = response.data.meta.last_page;
        total.value = response.data.meta.total;
        perPage.value = response.data.meta.per_page;
    } catch (error) {
        console.error('Failed to fetch transactions:', error);
    }
};

  const formatTime = (totalTime) => {
    const minutes = Math.floor(totalTime / 60)
    const seconds = Math.round(totalTime % 60)
      .toString()
      .padStart(2, '0')
    return `${minutes}:${seconds}`
  }
  const formatDate = (dateString) => {
    const date = new Date(dateString)
    const day = String(date.getDate()).padStart(2, '0')
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const year = date.getFullYear()
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    const seconds = String(date.getSeconds()).padStart(2, '0')
    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`
  }

  return {
    transactions,
    totalTransactions,
    currentPage,
    lastPage,
    total,
    perPage,
    formatTime,
    formatDate,
    fetchTransactions
  }
})
