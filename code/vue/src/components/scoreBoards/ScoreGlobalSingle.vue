<script setup>
import BestTimes from '@/components/scoreBoards/ScoreGlobalSingleBestTimes.vue';
import MinTurns from '@/components/scoreBoards/ScoreGlobalSingleMinTurns.vue';

const props = defineProps({
    bestTimes: Object,
    minTurns: Object
});
</script>

<template>
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
            <h3 class="text-2xl font-bold text-white tracking-wide">
                Singleplayer
            </h3>
        </div>

        <div class="grid md:grid-cols-2 gap-6 p-6">
            <BestTimes :bestTimes="bestTimes" />
            <MinTurns :minTurns="minTurns" />
        </div>
    </div>
</template>