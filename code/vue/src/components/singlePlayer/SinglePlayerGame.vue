<script setup>
import { ref, computed, onMounted } from 'vue'
import Board from './Board.vue'
import { useMemoryGame } from './memory'

// Array of board configurations
const boardConfigurations = [
  { name: '3x4', cols: 4, rows: 3 },
  { name: '4x4', cols: 4, rows: 4 },
  { name: '5x4', cols: 4, rows: 5 },
  { name: '6x6', cols: 6, rows: 6 }
]
const board = ref(null)

const currentStatus = ref(null)

const statusChanged = (newStatus) => {
  currentStatus.value = newStatus
}

// Selected board configuration
const selectedBoard = ref(boardConfigurations[0])

// Game status message
const statusMessage = computed(() => {
  if (currentStatus.value === 'E') {
    return `Congratulations! You completed the game in ${moves.value} moves.`
  }
  // return `Matches: ${matches.value}/${selectedBoard.value.pairs}. Moves: ${moves.value}`
})

// Restart the game
const restart = () => {
  board.value.start()
}
onMounted(() => {
  restart()
})
</script>

<template>
  <div class="p-8 mx-auto max-w-3xl min-w-96 bg-white shadow-lg rounded-lg">
    <div class="mb-6">
      <label class="block text-lg font-semibold mb-2 text-gray-800">Choose Board Size:</label>
      <select
        v-model="selectedBoard"
        class="w-full p-3 rounded-lg border-gray-300 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option v-for="config in boardConfigurations" :key="config.name" :value="config">
          {{ config.name }}
        </option>
      </select>
    </div>

    <!-- Game Status -->
    <div v-if="currentStatus !== 'idle'" class="my-4">
      <p class="text-lg font-semibold text-gray-800">{{ statusMessage }}</p>
    </div>

    <!-- Start/Play Button -->
    <div class="my-6 text-center">
      <button
        @click="startGame"
        class="rounded-lg bg-green-600 text-white py-2 px-6 mb-4 hover:bg-green-700 transition duration-300 ease-in-out transform hover:scale-105"
      >
        Start Game
      </button>
    </div>

    <!-- Victory Screen -->
    <div
      v-if="currentStatus === 'E'"
      class="my-6 p-6 bg-green-100 rounded-xl flex items-center justify-between shadow-xl"
    >
      <div class="flex-grow text-center">
        <h2 class="text-2xl font-bold text-green-800 mb-2">Congratulations!</h2>
        <p class="text-lg text-green-700">{{ statusMessage }}</p>
      </div>

      <button
        type="button"
        class="rounded bg-green-700 py-2 px-3 m-0.5 text-white flex shadow-lg shadow-green-900"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
          />
        </svg>
        <span class="ps-2 text-base" @click="restart">Restart</span>
      </button>
    </div>
    <Board ref="board" @statusChanged="statusChanged"></Board>
  </div>
</template>
