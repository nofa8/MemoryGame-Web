// stores/statistics.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios';
import { useErrorStore } from '@/stores/error'

export const useStatisticsStore = defineStore('statistics', () => {
    const storeAuth = useAuthStore()
    const isAdmin = computed(() => storeAuth.user?.type === 'A')
    
    const totalPlayers = ref(0)
    const totalGames = ref(0)
    const gamesLastWeek = ref(0)
    const gamesLastMonth = ref(0)
    const purchasesByTimePeriod = ref([])
    const purchasesByPlayer = ref([])
    const availableYears = ref([])
    
    const fetchStatistics = async (year = null) => {
        try {
            const params = {}
            if (year) params.year = year
            
            const response = await axios.get('/statistics', { params })
            
            totalPlayers.value = response.data.totalPlayers
            totalGames.value = response.data.totalGames
            gamesLastWeek.value = response.data.gamesLastWeek
            gamesLastMonth.value = response.data.gamesLastMonth
            availableYears.value = response.data.availableYears
            
            if (isAdmin.value) {
                purchasesByTimePeriod.value = response.data.purchasesByTimePeriod
                purchasesByPlayer.value = response.data.purchasesByPlayer
            }
        } catch (error) {
            console.error('Failed to fetch statistics:', error)
            useErrorStore().setError('Failed to fetch statistics')
        }
    }

    return {
        totalPlayers,
        totalGames,
        gamesLastWeek,
        gamesLastMonth,
        purchasesByTimePeriod,
        purchasesByPlayer,
        availableYears,
        fetchStatistics
    }
});