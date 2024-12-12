<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const emit = defineEmits(['apply-filters']);
const authStore = useAuthStore();

const boardSize = ref('All board sizes');
const startDate = ref('');
const endDate = ref('');
const gameType = ref('All types');
const creator = ref('');

const boardSizes = [
    'All board sizes',
    '3x4',
    '4x4',
    '6x6'
];

const gameTypes = [
    'All types',
    'Single Player',
    'Multiplayer'
];

const showCreatorFilter = computed(() => {
    return authStore.user?.type === 'A';
});

const applyFilters = () => {
    const filters = {
        board_size: boardSize.value,
        start_date: startDate.value,
        end_date: endDate.value,
        game_type: gameType.value,
        ...(showCreatorFilter.value && creator.value && { creator: creator.value })
    };

    Object.keys(filters).forEach(key =>
        filters[key] === '' && delete filters[key]
    );

    emit('apply-filters', filters);
};

const resetFilters = () => {
    boardSize.value = 'All board sizes';
    startDate.value = '';
    endDate.value = '';
    gameType.value = 'All types';
    creator.value = '';
    applyFilters();
};
</script>

<template>
    <div class="bg-gray-100 p-4 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <select v-model="boardSize"
                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option v-for="size in boardSizes" :key="size" :value="size">
                    {{ size }}
                </option>
            </select>
            <input type="date" v-model="startDate"
                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <input type="date" v-model="endDate"
                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <select v-model="gameType"
                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option v-for="type in gameTypes" :key="type" :value="type">
                    {{ type }}
                </option>
            </select>

            <input v-if="showCreatorFilter" type="text" v-model="creator" placeholder="Creator Name"
                class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <div class="flex space-x-2 col-span-1 md:col-span-5">
                <button @click="applyFilters"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Apply Filters
                </button>
                <button @click="resetFilters"
                    class="w-full md:w-auto px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Reset
                </button>
            </div>
        </div>
    </div>
</template>