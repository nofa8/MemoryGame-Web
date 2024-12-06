<script setup>
import { onMounted } from 'vue';
import { useHistoryStore } from '@/stores/history';

const historyStore = useHistoryStore();

onMounted(() => {
    historyStore.fetchGameHistory();
});
</script>

<template>
    <div class="container mx-auto p-4">
        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-center">Start Date</th>
                        <th class="px-4 py-3 text-center">Board Size</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Total Game Time</th>
                        <th class="px-4 py-3 text-center">Turns</th>
                        <th class="px-4 py-3 text-center">Players</th>
                        <th class="px-4 py-3 text-center">Winner</th>
                        <th class="px-4 py-3 text-center">Creator</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <tr v-for="game in historyStore.games" :key="game.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-center">{{ historyStore.formatDate(game.start_time) }}</td>
                        <td class="px-4 py-3 text-center">{{ game.board_rows }} x {{ game.board_cols }}</td>
                        <td class="px-4 py-3 text-center">{{ historyStore.mapStatus(game.status) }}</td>
                        <td class="px-4 py-3 text-center">{{ historyStore.formatTime(game.total_time) }}</td>
                        <td class="px-4 py-3 text-center">{{ game.total_turns }}</td>
                        <td class="px-4 py-3 text-center">{{ game.players.join(' vs ') }}</td>
                        <td class="px-4 py-3 text-center">{{ game.winner }}</td>
                        <td class="px-4 py-3 text-center">{{ game.creator }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between mt-4">
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded" :disabled="historyStore.currentPage === 1"
                @click="historyStore.fetchGameHistory(historyStore.currentPage - 1)">
                Previous
            </button>

            <span class="self-center">
                Page {{ historyStore.currentPage }} of {{ historyStore.lastPage }}
            </span>

            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded"
                :disabled="historyStore.currentPage === historyStore.lastPage"
                @click="historyStore.fetchGameHistory(historyStore.currentPage + 1)">
                Next
            </button>
        </div>
    </div>
</template>