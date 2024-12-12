<script setup>
import { ref, onMounted } from 'vue';
import { useHistoryStore } from '@/stores/history';
import HistoryGame from './HistoryGame.vue';
import HistoryFilters from './HistoryFilters.vue';

const historyStore = useHistoryStore();
const filters = ref({});

const handleFilters = (newFilters) => {
    filters.value = newFilters;
    historyStore.fetchGameHistory(1, filters.value);
};

const fetchPage = (page) => {
    historyStore.fetchGameHistory(page, filters.value);
};

onMounted(() => {
    historyStore.fetchGameHistory();
});
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">Game History</h2>
            </div>

            <HistoryFilters @apply-filters="handleFilters" />

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start Date
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Board Size
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Game Time
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Turns
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Players
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Winner
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Creator
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <HistoryGame v-for="game in historyStore.games" :key="game.id" :game="game" />
                    </tbody>
                </table>
                <div v-if="historyStore.games.length === 0" class="text-center py-10 text-gray-500">
                    No games found in history
                </div>
            </div>

            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between">
                    <button @click="fetchPage(historyStore.currentPage - 1)"
                        :disabled="historyStore.currentPage === 1"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-center">
                        <p class="text-sm text-gray-700">
                            Page
                            <span class="font-medium">{{ historyStore.currentPage }}</span>
                            of
                            <span class="font-medium">{{ historyStore.lastPage }}</span>
                        </p>
                    </div>
                    <button @click="fetchPage(historyStore.currentPage + 1)"
                        :disabled="historyStore.currentPage === historyStore.lastPage"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>