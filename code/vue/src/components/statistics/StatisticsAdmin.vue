<script setup>
import { useStatisticsStore } from '@/stores/statistics'
import { Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)
const statisticsStore = useStatisticsStore()
</script>
<template>
    <div class="space-y-8">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">
            Admin Statistics
        </h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 hover:shadow-2xl transition duration-300">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                    Purchases by Time Period
                </h3>
                <div class="h-48 sm:h-56">
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
                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">
                    Top 10 Players by Purchases
                </h3>
                <div class="h-48 sm:h-56">
                    <Pie :data="{
                        labels: statisticsStore.purchasesByPlayer.slice(0, 10).map(item => item.player),
                        datasets: [{
                            label: 'Top 5 Players Purchases',
                            data: statisticsStore.purchasesByPlayer.slice(0, 10).map(item => item.total),
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