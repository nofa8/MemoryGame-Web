<script setup>
import { useStatisticsStore } from '@/stores/statistics'

const statisticsStore = useStatisticsStore()
</script>
<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
      <div v-for="(stat, index) in [
        { title: 'Total Players', value: statisticsStore.totalPlayers },
        { title: 'Total Games', value: statisticsStore.totalGames },
        { title: 'Games Last Week', value: statisticsStore.gamesLastWeek },
        { title: 'Games Last Month', value: statisticsStore.gamesLastMonth }
      ]" :key="stat.title" class="transition duration-300 transform hover:scale-105 hover:shadow-lg">
        <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base sm:text-xl font-semibold text-gray-700 mb-2">
                {{ stat.title }}
              </h3>
              <p class="text-2xl sm:text-3xl font-bold text-blue-600">
                {{ stat.value }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>