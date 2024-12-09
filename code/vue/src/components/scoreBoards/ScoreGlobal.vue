<script setup>
import { storeToRefs } from 'pinia';
import { onMounted } from 'vue';
import { useScoreboardStore } from '@/stores/scoreboard';

const scoreboardStore = useScoreboardStore();
const { bestTimes, minTurns, topPlayers } = storeToRefs(scoreboardStore);

onMounted(() => {
  scoreboardStore.fetchScoreGlobal();
});
</script>

<template>
  <div class="container mx-auto p-4 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Global Scoreboards</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
      <h3 class="text-xl font-semibold text-gray-700 mb-4">Single Player Statistics</h3>

      <div class="mb-4">
        <h4 class="text-lg font-medium text-gray-600 mb-3">Best Times by Board Size</h4>
        <ul v-if="Object.keys(bestTimes).length" class="space-y-2">
          <li v-for="(game, boardId) in bestTimes" :key="boardId" class="flex justify-between bg-gray-100 p-2 rounded">
            <span>{{ game.board_cols }}x{{ game.board_rows }} Board</span>
            <span class="font-bold">{{ scoreboardStore.formatTime(game.total_time) }} seconds ({{ game.nickname }})</span>
          </li>
        </ul>
        <p v-else class="text-gray-500">No best times recorded</p>
      </div>

      <div>
        <h4 class="text-lg font-medium text-gray-600 mb-3">Minimum Turns by Board Size</h4>
        <ul v-if="Object.keys(minTurns).length" class="space-y-2">
          <li v-for="(game, boardId) in minTurns" :key="boardId" class="flex justify-between bg-gray-100 p-2 rounded">
            <span>{{ game.board_cols }}x{{ game.board_rows }} Board</span>
            <span class="font-bold">{{ game.total_turns }} turns ({{ game.nickname }})</span>
          </li>
        </ul>
        <p v-else class="text-gray-500">No minimum turns recorded</p>
      </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
      <h3 class="text-xl font-semibold text-gray-700 mb-4">Multiplayer Statistics</h3>

      <div>
        <h4 class="text-lg font-medium text-gray-600 mb-3">Top 5 Players with Most Victories</h4>
        <ul v-if="topPlayers.length" class="space-y-2">
          <li v-for="player in topPlayers" :key="player.user_id" class="flex justify-between bg-gray-100 p-2 rounded">
            <span>{{ player.nickname }}</span>
            <span class="font-bold">{{ player.total_victories }} victories</span>
          </li>
        </ul>
        <p v-else class="text-gray-500">No top players recorded</p>
      </div>
    </div>
  </div>
</template>