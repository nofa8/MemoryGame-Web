<script setup>
import { useScoreboardStore } from '@/stores/scoreboard';

const scoreboardStore = useScoreboardStore();

const props = defineProps({
    bestTimes: Object
});
</script>

<template>
    <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-100 border-b border-blue-200 p-4">
            <h4 class="text-xl font-semibold text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Best Times by Board Size
            </h4>
        </div>
        <div v-if="Object.keys(bestTimes).length" class="divide-y divide-blue-200">
            <div v-for="(game, boardId) in bestTimes" :key="boardId"
                class="p-4 bg-white hover:bg-blue-50 transition duration-200 flex justify-between items-center">
                <span class="text-gray-700">{{ game.board_cols }}x{{ game.board_rows }} Board</span>
                <div class="text-right">
                    <span class="font-bold text-blue-800">
                        {{ scoreboardStore.formatTime(game.total_time) }} seconds
                    </span>
                    <div class="text-sm text-gray-500">{{ game.nickname }}</div>
                </div>
            </div>
        </div>
        <p v-else class="p-4 text-gray-500 text-center">No best times recorded</p>
    </div>
</template>