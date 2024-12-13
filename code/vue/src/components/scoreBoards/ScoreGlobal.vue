<script setup>
import { storeToRefs } from 'pinia';
import { onMounted } from 'vue';
import { useScoreboardStore } from '@/stores/scoreboard';
import SingleplayerStatistics from '@/components/scoreBoards/ScoreGlobalSingle.vue';
import MultiplayerStatistics from '@/components/scoreBoards/ScoreGlobalMulti.vue';

const scoreboardStore = useScoreboardStore();
const { bestTimes, minTurns, topPlayers } = storeToRefs(scoreboardStore);
onMounted(() => {
  scoreboardStore.fetchScoreGlobal();
});
</script>

<template>
  <div class="container mx-auto p-4 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Global Scoreboards</h2>
    <SingleplayerStatistics :bestTimes="bestTimes" :minTurns="minTurns" />
    <MultiplayerStatistics :topPlayers="topPlayers" />
  </div>
</template>