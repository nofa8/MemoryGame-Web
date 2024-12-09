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
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Multiplayer Statistics</h3>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-green-100 p-4 rounded-lg">
                <h4 class="text-lg font-medium text-green-800">Total Victories</h4>
                <p class="text-2xl font-bold text-green-700">
                    {{ scoreboardStore.multiplayerStats.total_victories }}
                </p>
            </div>

            <div class="bg-red-100 p-4 rounded-lg">
                <h4 class="text-lg font-medium text-red-800">Total Losses</h4>
                <p class="text-2xl font-bold text-red-700">
                    {{ scoreboardStore.multiplayerStats.total_losses }}
                </p>
            </div>
        </div>
    </div>
</template>