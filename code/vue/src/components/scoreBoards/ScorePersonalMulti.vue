<script setup>
import { useScoreboardStore } from '@/stores/scoreboard';
import { onMounted } from 'vue';
const scoreboardStore = useScoreboardStore();
onMounted(() => {
    scoreboardStore.fetchScoreboardPersonal();
});
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
                <h3 class="text-2xl font-bold text-white tracking-wide">
                    Multiplayer Statistics
                </h3>
            </div>

            <div class="grid md:grid-cols-2 gap-6 p-6">
                <div class="bg-green-50 border border-green-200 rounded-lg shadow-md overflow-hidden">
                    <div class="bg-green-100 border-b border-green-200 p-4">
                        <h4 class="text-xl font-semibold text-green-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Victories
                        </h4>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-4xl font-bold text-green-700">
                            {{ scoreboardStore.multiplayerStats?.total_victories || 0 }}
                        </p>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg shadow-md overflow-hidden">
                    <div class="bg-red-100 border-b border-red-200 p-4">
                        <h4 class="text-xl font-semibold text-red-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-red-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Losses
                        </h4>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-4xl font-bold text-red-700">
                            {{ scoreboardStore.multiplayerStats?.total_losses || 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>