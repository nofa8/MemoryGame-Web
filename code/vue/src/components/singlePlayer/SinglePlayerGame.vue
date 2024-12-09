<script setup>
import { ref } from 'vue'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'

import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'
import { FingerprintSpinner } from 'epic-spinners'
import { useBoardsStore } from '@/stores/board'

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const router = useRouter()

const isLoading = ref(false)
const boardsStore = useBoardsStore()
boardsStore.loadBoards()

const startGame = async (board) => {
  isLoading.value = true
  try {
    if (!storeAuth.user) {
      router.push({ name: 'game' })
      return
    }

    const payload = {
      created_user_id: storeAuth.user.id,
      type: 'S',
      board_id: board.id
    }

    const response = await axios.post('/games', payload)
    const gameData = response.data.data
    
    router.push({
      name: 'game',
      query: {
        game_id: gameData.id,
        board_cols: board.cols,
        board_rows: board.rows
      }
    })
  } catch (e) {
    storeError.setErrorMessages(
      e.response?.data?.message || 'An error occurred',
      e.response?.data?.errors || {},
      e.response?.status || 500,
      'Error Starting Game'
    )
  } finally {
    isLoading.value = false
  }
}

const checkAccessBoard = (board) => {
  return (board.cols === 3) || (storeAuth.user && storeAuth.user.brain_coins_balance  >= 1)
}
</script>

<template>
  <div class="relative">
    <!-- Loading Spinner -->
    <div
      v-if="isLoading"
      class="absolute inset-0 flex flex-col justify-center items-center bg-gray-900 bg-opacity-80 z-50"
    >
      <div class="text-xl text-white mb-4 animate-pulse">Creating your game...</div>
      <fingerprint-spinner :animation-duration="1500" :size="80" color="#4f46e5" />
    </div>

    <div class="flex flex-col justify-center items-center p-6 space-y-8">
      <!-- Card -->
      <Card
        class="w-full max-w-5xl rounded-lg shadow-xl bg-gradient-to-br from-gray-50 to-gray-200 dark:from-gray-800 dark:to-gray-900"
      >
        <CardHeader class="p-6">
          <CardTitle class="text-2xl font-semibold text-center text-gray-800 dark:text-white">
            Choose a Board
          </CardTitle>
          <CardDescription class="text-center text-gray-600 dark:text-gray-400">
            Select the board you want to play on and start your adventure!
          </CardDescription>
        </CardHeader>
        <CardContent class="p-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Board Options -->
            <div
              v-for="board in boardsStore.boards"
              :key="board.id"
              class="relative group flex flex-col items-center justify-center p-6 rounded-lg cursor-pointer transition-all transform hover:scale-105 shadow-md"
              :class="
                checkAccessBoard(board)
                  ? 'bg-blue-500 text-white'
                  : 'bg-gray-400 text-gray-700 cursor-not-allowed'
              "
              @click="checkAccessBoard(board) && startGame(board)"
            >
              <h2 class="text-lg font-bold mb-2">Board: {{ board.cols }} x {{ board.rows }}</h2>
              <p v-if="!checkAccessBoard(board)" class="text-sm italic">Unavailable</p>

              <!-- Lock Icon for Unavailable Boards -->
              <svg
                v-if="!checkAccessBoard(board)"
                xmlns="http://www.w3.org/2000/svg"
                class="absolute bottom-4 right-4 h-6 w-6 fill-current text-gray-200 opacity-50 group-hover:opacity-75"
                viewBox="0 -960 960 960"
              >
                <path
                  d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm240-120q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80ZM240-160v-400 400Z"
                />
              </svg>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
