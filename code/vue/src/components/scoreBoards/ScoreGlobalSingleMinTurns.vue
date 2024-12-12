<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    minTurns: Object
});
</script>

<template>
    <div class="bg-green-50 border border-green-200 rounded-lg shadow-md overflow-hidden">
        <div class="bg-green-100 border-b border-green-200 p-4">
            <h4 class="text-xl font-semibold text-green-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Minimum Turns by Board Size
            </h4>
        </div>
        <div v-if="Object.keys(minTurns).length" class="divide-y divide-green-200">
            <div v-for="(game, boardId) in minTurns" :key="boardId"
                class="p-4 bg-white hover:bg-green-50 transition duration-200 flex justify-between items-center">
                <span class="text-gray-700">{{ game.board_cols }}x{{ game.board_rows }} Board</span>
                <div class="text-right">
                    <span class="font-bold text-green-800">
                        {{ game.total_turns }} turns
                    </span>
                    <div class="text-sm text-gray-500">{{ game.nickname }}</div>
                </div>
            </div>
        </div>
        <p v-else class="p-4 text-gray-500 text-center">No minimum turns recorded</p>
    </div>
</template>