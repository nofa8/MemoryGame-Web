<script setup>
import { ref, watch } from 'vue'
import { useStatisticsStore } from '@/stores/statistics'
import { Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)
const statisticsStore = useStatisticsStore()

const topPlayersLimit = ref(5)
const selectedYear = ref(new Date().getFullYear())

watch(selectedYear, year => {
    statisticsStore.fetchStatistics({
        year
    })
}, { immediate: true })
</script>

<template>
    <div class="space-y-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">
            Admin Statistics
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 hover:shadow-2xl transition duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">
                        Purchases by Time Period
                    </h3>
                    <div class="flex flex-col gap-1">
                        <select id="year-select" v-model="selectedYear"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option v-for="year in statisticsStore.availableYears" :key="year" :value="year">
                                {{ year }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="h-48 sm:h-56 mt-4">
                    <Bar :data="{
                        labels: statisticsStore.purchasesByTimePeriod.map(item => item.period),
                        datasets: [{
                            label: 'Total Purchases',
                            data: statisticsStore.purchasesByTimePeriod.map(item => item.total),
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 2
                        }]
                    }" :options="{
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        padding: 10
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        padding: 5
                    }
                }
            }
        }" />
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 hover:shadow-2xl transition duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">
                        Top Players by Purchases
                    </h3>
                    <select v-model="topPlayersLimit"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="5">Top 5</option>
                        <option value="10">Top 10</option>
                        <option value="20">Top 20</option>
                    </select>
                </div>
                <div class="h-48 sm:h-56">
                    <Pie :data="{
                        labels: statisticsStore.purchasesByPlayer.slice(0, topPlayersLimit).map(item => item.player),
                        datasets: [{
                            label: `Top ${topPlayersLimit} Players Purchases`,
                            data: statisticsStore.purchasesByPlayer.slice(0, topPlayersLimit).map(item => item.total),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.6)',
                                'rgba(34, 197, 94, 0.6)',
                                'rgba(249, 115, 22, 0.6)',
                                'rgba(14, 165, 233, 0.6)',
                                'rgba(236, 72, 153, 0.6)',
                                'rgba(255, 205, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)',
                                'rgba(139, 0, 0, 0.6)'
                            ],
                            borderColor: 'rgba(255,255,255,0.8)',
                            borderWidth: 2
                        }]
                    }" :options="{
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 20,
                                    padding: 10
                                }
                            }
                        }
                    }" />
                </div>
            </div>
        </div>
    </div>
</template>