<script setup>


const props = defineProps({
    minTurns: Object
});
</script>

<template>
    <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md">
        <div class="bg-green-100 border-b border-green-200 p-4">
            <h4 class="text-xl font-semibold text-green-800">
                🎯 Top 3 Minimum Turns by Board Size
            </h4>
        </div>

        <div v-if="Object.keys(minTurns).length" class="divide-y divide-gray-200">
            <template v-for="(turns, boardSize) in minTurns" :key="boardSize">
                <div class="p-4 bg-white hover:bg-green-50 transition duration-200">
                    <div class="text-lg font-medium text-gray-700 mb-2">
                        {{ boardSize }} Board
                    </div>
                    <div v-for="(game, index) in turns" :key="index"
                        class="flex justify-between items-center mb-1 last:mb-0">
                        <span class="text-gray-600 flex items-center">
                            <span class="mr-2 text-sm bg-green-500 text-white rounded-full w-6 h-6 
                                            inline-flex items-center justify-center">
                                {{ index + 1 }}
                            </span>
                            Rank
                        </span>
                        <span class="font-bold text-green-800">
                            {{ game.total_turns_winner }} turns
                        </span>
                    </div>
                </div>
            </template>
        </div>
        <p v-else class="p-4 text-gray-500 text-center">
            No minimum turns recorded
        </p>
    </div>
</template>