<script setup>
import { useScoreboardStore } from '@/stores/scoreboard';
import { onMounted } from 'vue';
import BestTimes from '@/components/scoreBoards/ScorePersonalSingleBestTimes.vue';
import MinTurns from '@/components/scoreBoards/ScorePersonalSingleMinTurns.vue';

const scoreboardStore = useScoreboardStore();
onMounted(() => {
    scoreboardStore.fetchScoreboardPersonal();
});
</script>

<template>
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6">
            <h3 class="text-2xl font-bold text-white tracking-wide">
                Singleplayer
            </h3>
        </div>

        <div class="grid md:grid-cols-2 gap-8 p-6">
            <BestTimes :bestTimes="scoreboardStore.bestTimes" />
            <MinTurns :minTurns="scoreboardStore.minTurns" />
        </div>
    </div>
</template>