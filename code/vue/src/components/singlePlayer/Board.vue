<script setup>
import { ref, computed, watch } from 'vue'
import { inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Card as CardCard } from '@/components/ui/card'
import { useStopwatch } from 'vue-timer-hook'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
// import axios from 'axios'
import { useMemoryGame } from './memory'
import { useCounterStore } from '@/stores/counter'
import CardComponent from './CardComponent.vue'

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const storeTurns = useCounterStore()
const route = useRoute()
const router = useRouter()

const game_id = ref(null)
const rows = ref(0)
const cols = ref(0)
const lastMoveDone = ref(0)
game_id.value = route.query?.game_id ?? null
rows.value = route.query?.board_rows ?? 4
cols.value = route.query?.board_cols ?? 3

const gameInterrupted = computed(() => {
  return showSeconds.value - lastMoveDone.value >= 20
})
// currentPlayer
const { status, board, moves, matchedPairs, start, play } = useMemoryGame(rows.value, cols.value)

const alertDialog = inject('alert_dialog_nice')
const autoStart = true
const stopwatch = useStopwatch(autoStart)

const showSeconds = computed(() => {
  return stopwatch.seconds.value + 60 * stopwatch.minutes.value
})

const turns = computed(() => {
  return moves
})
const flip = (index) => {
  play(index)
  storeTurns.increment()
}
watch(status, (newValue) => {
  if (newValue === '1') {
    stopwatch.pause()
    alertDialog.value.open(
      router.push({ name: 'singlePlayerGames' }),
      'Game Won!',
      'Exit',
      'Next',
      `The game has ended! 🏆\n\nWould you like to go to the game selecter and challenge yourself again, or exit the game?\n\nYour choice will determine your next step!` // Description text
    )

    // if (storeAuth.user != null) {
    //   try {
    //     /*const payload = {
    //     created_user_id: storeAuth.user.id,
    //     type: 'S',
    //     board_id: board.id,
    //     status: 'E'
    //     };*/
    //     const payload = {
    //       status: 'E'
    //     }
    //     console.log(game_id.value)
    //     const response = axios.put(`/games/${game_id.value}`, payload).then((response) => {
    //       //console.log(response.data.data)
    //       //fazer update das coins visualmente
    //       router.push({ name: 'gameMode' })
    //       /* fazer aqui o depois

    //         isLoading.value = false
    //         return response*/
    //     })
    //   } catch (e) {
    //     console.log(e)
    //     storeError.setErrorMessages(
    //       e.response.data.message,
    //       e.response.data.errors,
    //       e.response.status,
    //       'Getting Games Error!'
    //     )
    //   }
    // }
  }
})

watch(gameInterrupted, (newValue) => {
  if (newValue === true) {
    stopwatch.pause()
    // alertDialog.value.open(
    //   goToGamehistory,
    //   'Are you sure?',
    //   'Cancel',

    // )

    // if (storeAuth.user != null) {
    //   try {
    //     /*const payload = {
    //     created_user_id: storeAuth.user.id,
    //     type: 'S',
    //     board_id: board.id,
    //     status: 'E'
    //     };*/
    //     const payload = {
    //       status: 'I'
    //     }
    //     console.log(game_id.value)
    //     const response = axios.put(`/games/${game_id.value}`, payload).then((response) => {
    //       //console.log(response.data.data)
    //       //fazer update das coins visualmente
    //       router.push({ name: 'gameMode' })
    //       /* fazer aqui o depois

    //         isLoading.value = false
    //         return response*/
    //     })
    //   } catch (e) {
    //     console.log(e)
    //     storeError.setErrorMessages(
    //       e.response.data.message,
    //       e.response.data.errors,
    //       e.response.status,
    //       'Getting Games Error!'
    //     )
    //   }
    // }
  }
})

const gridClasses = computed(() => `grid grid-cols-${cols.value}`)

start()
</script>
<template>
  <div class="flex justify-center items-center min-h-screen p-4 bg-gray-50 dark:bg-gray-900">
    <CardCard
      class="w-full max-w-6xl mx-auto h-auto rounded-lg bg-white dark:bg-gray-800 border-0 shadow-md p-6"
    >
      <div class="text-center">
        <!-- Header Section -->
        <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-xl shadow-lg mx-auto my-6">
          <div class="mb-4">
            <p class="text-2xl sm:text-3xl font-bold text-black dark:text-white mb-2">
              Memory Game
            </p>
            <hr class="border-gray-300 dark:border-gray-700" />
          </div>
          <div class="space-y-2 sm:space-y-4">
            <p class="text-lg sm:text-xl text-gray-800 dark:text-gray-300">
              ⏱ Time:
              <span class="font-semibold text-black dark:text-white">{{ showSeconds }}</span>
            </p>
            <p class="text-lg sm:text-xl text-gray-800 dark:text-gray-300">
              📋 Moves: <span class="font-semibold text-black dark:text-white">{{ turns }}</span>
            </p>
          </div>
        </div>

        <!-- Game Grid Section -->
        <div class="flex justify-center items-center mt-6">
          <div :class="gridClasses" class="grid gap-2 sm:gap-4 p-2 sm:p-4">
            <CardComponent
              v-for="(card, idx) in board"
              :key="idx"
              :piece="card.value"
              :index="idx"
              :is-flipped="card.isFlipped"
              :is-matched="card.isMatched"
              @flip="flip"
              class="w-16 sm:w-24 lg:w-28 bg-white dark:bg-gray-800 rounded-md shadow-md"
            />
          </div>
        </div>

        <!-- Restart Button -->
        <!-- <div class="mt-6">
          <button
            class="w-full sm:w-auto py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-md transition duration-300"
          >
            Restart Game
          </button>
        </div> -->
      </div>
    </CardCard>
  </div>
</template>
