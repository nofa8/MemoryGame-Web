<script setup>
import { onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useStatisticsStore } from '@/stores/statistics'
import CommonStatistics from './StatisticsCommon.vue'
import AdminStatistics from './StatisticsAdmin.vue'

const storeAuth = useAuthStore()
const isAdmin = computed(() => storeAuth.user?.type === 'A')

const statisticsStore = useStatisticsStore()

onMounted(statisticsStore.fetchStatistics)
</script>

<template>
  <div class="container mx-auto p-4 space-y-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Statistics</h2>
    <CommonStatistics />
    <div v-if="isAdmin">
      <AdminStatistics />
    </div>
  </div>
</template>