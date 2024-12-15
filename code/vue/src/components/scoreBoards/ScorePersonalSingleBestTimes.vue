<script setup>
import { useScoreboardStore } from '@/stores/scoreboard';

const scoreboardStore = useScoreboardStore();

const props = defineProps({
    bestTimes: Object
});
</script>

<template>
    <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md">
        <div class="bg-blue-100 border-b border-blue-200 p-4">
            <h4 class="text-xl font-semibold text-blue-800">
                🏆 Top 3 Best Times by Board Size
            </h4>
        </div>

        <div v-if="Object.keys(bestTimes).length" class="divide-y divide-gray-200">
            <template v-for="(times, boardSize) in bestTimes" :key="boardSize">
                <div class="p-4 bg-white hover:bg-blue-50 transition duration-200">
                    <div class="text-lg font-medium text-gray-700 mb-2">
                        {{ boardSize }} Board
                    </div>
                    <div v-for="(game, index) in times" :key="index"
                        class="flex justify-between items-center mb-1 last:mb-0">
                        <span class="text-gray-600 flex items-center">
                            <span class="mr-2 text-sm bg-blue-500 text-white rounded-full w-6 h-6 
                                            inline-flex items-center justify-center">
                                {{ index + 1 }}
                            </span>
                            Rank
                        </span>
                        <span class="font-bold text-blue-800">
                            {{ scoreboardStore.formatTime(game.total_time) }} seconds
                        </span>
                    </div>
                </div>
            </template>
        </div>
        <p v-else class="p-4 text-gray-500 text-center">
            No best times recorded
        </p>
    </div>
</template>