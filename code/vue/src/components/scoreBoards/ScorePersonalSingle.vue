<script setup>
import { useScoreboardStore } from '@/stores/scoreboard';
import { onMounted } from 'vue';

const scoreboardStore = useScoreboardStore();

onMounted(() => {
    scoreboardStore.fetchScoreboardPersonal();
});
</script>

<template>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Single Player Statistics</h3>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-lg font-medium text-gray-600 mb-3">Best Times by Board Size</h4>
                <ul v-if="Object.keys(scoreboardStore.bestTimes).length" class="space-y-2">
                    <li v-for="(game, boardId) in scoreboardStore.bestTimes" :key="boardId"
                        class="flex justify-between bg-gray-100 p-2 rounded">
                        <span>{{ game.board_cols }}x{{ game.board_rows }} Board</span>
                        <span class="font-bold">{{ scoreboardStore.formatTime(game.total_time) }} seconds</span>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No best times recorded</p>
            </div>

            <div>
                <h4 class="text-lg font-medium text-gray-600 mb-3">Minimum Turns by Board Size</h4>
                <ul v-if="Object.keys(scoreboardStore.minTurns).length" class="space-y-2">
                    <li v-for="(game, boardId) in scoreboardStore.minTurns" :key="boardId"
                        class="flex justify-between bg-gray-100 p-2 rounded">
                        <span>{{ game.board_cols }}x{{ game.board_rows }} Board</span>
                        <span class="font-bold">{{ game.total_turns_winner }} turns</span>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No minimum turns recorded</p>
            </div>
        </div>
    </div>
</template>