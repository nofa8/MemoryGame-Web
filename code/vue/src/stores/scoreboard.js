import { defineStore } from 'pinia';
import { ref } from 'vue'
import axios from 'axios';
import { useErrorStore } from '@/stores/error'

export const useScoreboardStore = defineStore('scoreboard', () => {
    const bestTimes = ref({});
    const minTurns = ref({});
    const multiplayerStats = ref({
        total_victories: 0,
        total_losses: 0
    });

    const formatTime = (totalTime) => {
        const minutes = Math.floor(totalTime / 60)
        const seconds = Math.round(totalTime % 60).toString().padStart(2, '0')
        return `${minutes}:${seconds}`;
    }

    const fetchScoreboardPersonal = async () => {
        try {
            const response = await axios.get('scoreboardPersonal');
            bestTimes.value = response.data.best_times;
            minTurns.value = response.data.min_turns;
            multiplayerStats.value = response.data.multiplayer_stats;
        } catch (error) {
            const errorStore = useErrorStore();
            errorStore.setErrorMessages('Failed to fetch personal scoreboard');
            console.error('Failed to fetch scoreboard:', error);
        }
    };

    return {
        bestTimes,
        minTurns,
        multiplayerStats,
        formatTime,
        fetchScoreboardPersonal
    }
})
