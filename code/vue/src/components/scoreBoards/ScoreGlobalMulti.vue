<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    topPlayers: Array
});
</script>

<template>
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
            <h3 class="text-2xl font-bold text-white tracking-wide">
                Multiplayer Statistics
            </h3>
        </div>

        <div class="p-6">
            <div class="bg-purple-50 border border-purple-200 rounded-lg shadow-md overflow-hidden">
                <div class="bg-purple-100 border-b border-purple-200 p-4">
                    <h4 class="text-xl font-semibold text-purple-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a4 4 0 00-4-4H8a4 4 0 00-4 4v2a2 2 0 002 2h8a2 2 0 002-2v-2a4 4 0 00-4-4z" />
                        </svg>
                        Top 5 Players with Most Victories
                    </h4>
                </div>
                <div v-if="topPlayers.length" class="divide-y divide-purple-200">
                    <div v-for="(player, index) in topPlayers" :key="player.user_id"
                        class="p-4 bg-white hover:bg-purple-50 transition duration-200 flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="mr-4 text-sm bg-purple-500 text-white rounded-full w-6 h-6 
                            inline-flex items-center justify-center">
                                {{ index + 1 }}
                            </span>
                            <span class="text-gray-700">{{ player.nickname }}</span>
                        </div>
                        <span class="font-bold text-purple-800">
                            {{ player.total_victories }} victories
                        </span>
                    </div>
                </div>
                <p v-else class="p-4 text-gray-500 text-center">No top players recorded</p>
            </div>
        </div>
    </div>
</template>