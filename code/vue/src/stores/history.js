import { defineStore } from 'pinia';
import { ref } from 'vue'
import axios from 'axios';
import { useErrorStore } from '@/stores/error'

export const useHistoryStore = defineStore('history', () => {
    const games = ref([])
    const currentPage = ref(1)
    const lastPage = ref(1)
    const total = ref(0)
    const perPage = ref(10)

    const storeError = useErrorStore()

    const mapStatus = (status) => {
        switch (status) {
            case 'E':
                return 'Ended';
            case 'I':
                return 'Interrupted';
            case 'PE':
                return 'Pending';
            case 'PL':
                return 'Progress';
            default:
                return 'Unknown';
        }
    }

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    };

    const formatTime = (totalTime) => {
        const minutes = Math.floor(totalTime / 60)
        const seconds = Math.round(totalTime % 60).toString().padStart(2, '0')
        return `${minutes}:${seconds}`;
    }

    const fetchGameHistory = async (page = 1, filters = {}) => {
        storeError.resetMessages();
        try {
            const queryParams = new URLSearchParams({
                page,
                ...filters
            }).toString();
    
            const response = await axios.get(`history?${queryParams}`);
            games.value = response.data.data;
            currentPage.value = response.data.meta.current_page;
            lastPage.value = response.data.meta.last_page;
            total.value = response.data.meta.total;
            perPage.value = response.data.meta.per_page;
        } catch (error) {
            console.error('Failed to fetch game history:', error);
            storeError.setErrorMessages(error.message);
        }
    };

    return {
        fetchGameHistory, 
        mapStatus, 
        formatTime, 
        formatDate, 
        games,
        currentPage,
        lastPage,
        total,
        perPage
    }
});