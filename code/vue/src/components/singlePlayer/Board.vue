//Board
<script setup>
import { ref, computed, watch, onMounted, onBeforeMount, onBeforeUnmount } from 'vue'
import { inject } from 'vue'
import { onBeforeRouteLeave, useRoute, useRouter } from 'vue-router'
import { Card as CardCard } from '@/components/ui/card'
import { useStopwatch } from 'vue-timer-hook'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { useMemoryGame } from './memory'
import CardComponent from './CardComponent.vue'

const storeAuth = useAuthStore()
const storeError = useErrorStore()
const route = useRoute()
const router = useRouter()

const leavingSound = new Audio('/leaving.mp3')
leavingSound.volume = 0.2
const game_id = ref(null)
const rows = ref(0)
const cols = ref(0)
const lastAction = ref(0)
const leaving = ref(false)
game_id.value = route.query?.game_id ?? null
rows.value = route.query?.board_rows ?? 4
cols.value = route.query?.board_cols ?? 3

const { status, board, moves, matchedPairs, streak, bestStreak, start, play } = useMemoryGame(
  rows.value,
  cols.value
)

const alertDialog = inject('alertDialog')
const autoStart = true
const stopwatch = useStopwatch(autoStart)

const showSeconds = computed(() => {
  return stopwatch.seconds.value + 60 * stopwatch.minutes.value
})

const dynamicBackground = computed(() => {
  const intensity = Math.max(0, 255 - streak.value * 40) // Adjust the multiplier for desired sensitivity
  const redColor = `rgba(255, ${intensity}, ${intensity}, 1)`
  const middleColor = `rgba(255, 255, 255, 1)` // White at the center

  return `linear-gradient(to right, ${redColor}, ${middleColor}, ${redColor})`
})

const stopWorking = ref(true)

const turns = computed(() => {
  return moves
})
const flip = (index) => {
  play(index)
  if (!stopWorking.value) {
    stopwatch.start()
  }
  lastAction.value = showSeconds.value
}

const mainPage = function () {
  router.push({ name: 'singlePlayerGames' })
}

watch(status, (newValue) => {
  if (newValue === '1') {
    stopwatch.pause()
    alertDialog.value.open(
      mainPage,
      'Game Won!',
      'Exit',
      'Next',
      `The game has ended! 🏆\n\nWould you like to go to the game selecter and challenge yourself again, or exit the game?\n\nYour choice will determine your next step!` // Description text
    )

    if (storeAuth.user != null) {
      try {
        const payload = {
          status: 'E',
          turns: moves.value
        }

        const response = axios.put(`/games/${game_id.value}`, payload).then((response) => {
          //console.log(response.data.data)
          //update visual do necessário
        })
      } catch (e) {
        console.log(e)
        storeError.setErrorMessages(
          e.response.data.message,
          e.response.data.errors,
          e.response.status,
          'Getting Games Error!'
        )
      }
    }
  }
})

watch(leaving, (newValue) => {
  if (newValue === true) {
    if (storeAuth.user != null) {
      try {
        const payload = {
          status: 'I'
        }

        const response = axios.put(`/games/${game_id.value}`, payload)
        // .then((response) => {
        //   //update visual do necessário
        // })
      } catch (e) {
        console.log(e)
        storeError.setErrorMessages(
          e.response.data.message,
          e.response.data.errors,
          e.response.status,
          'Getting Games Error!'
        )
      }
    } else {
      stopwatch.start()
    }
  }
})

// Incrível dor de cabeça pois o grid-cols-${cols.value} não é possível por causa de o tailwind não suportar grids dinâmicos
const gridClasses = computed(() => {
  const columnMap = {
    3: 'grid-cols-3',
    4: 'grid-cols-4',
    6: 'grid-cols-6'
  }
  return columnMap[cols.value] || 'grid-cols-3' // Default fallback
})

// beforeRouteLeave Guard to handle navigation when game is in progress
onBeforeRouteLeave((to, from, next) => {
  // Check if the game is still in progress and if status is not 1
  const isItLeaving = () => {
    leavingSound.play()
    leaving.value = true

    setTimeout(() => {
      next()
    }, 1000)
  }
  if (status.value !== '1') {
    stopwatch.pause()
    stopWorking.value = false
    alertDialog.value.open(
      isItLeaving, // Callback to proceed with navigation if user clicks 'Quit'
      'Do you want to quit?', // Title of the dialog
      'Continue', // Label for the button to continue the game
      'Quit', // Label for the button to quit the game
      '⚠️ Your game is still in progress! 🚧 Leaving now will interrupt your progress, and the game will be lost. ❓ Are you sure you want to exit?' // Alert description
    )
  } else {
    next() // Proceed with the navigation if game is finished
  }
})

const handleBeforeUnload = async (event) => {
  if (status.value === '0' && storeAuth.user != null) {
    // Show a confirmation dialog to the user
    event.preventDefault();
    event.returnValue = "Are you sure you want to leave the game? Your progress may be lost.";

    try {
      const payload = {
        status: 'I',
      };

      await axios.put(`/games/${game_id.value}`, payload);

      console.log("Game status updated successfully.");
    } catch (error) {
      console.error("Error updating game status:", error);
    }
  }
};



onBeforeUnmount(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload)
})

// Add event listener for beforeunload
window.addEventListener('beforeunload', handleBeforeUnload)

start()
</script>
<template>
  <div class="flex justify-center items-center min-h-screen p-4">
    <!-- Fire here please-->
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
            <p class="text-lg sm:text-xl text-gray-800 dark:text-gray-300">
              🔥 Current Streak:
              <span class="font-semibold text-black dark:text-white">{{ streak }}</span>
            </p>
            <p class="text-lg sm:text-xl text-gray-800 dark:text-gray-300">
              🏆 Best Streak:
              <span class="font-semibold text-black dark:text-white">{{ bestStreak }}</span>
            </p>
          </div>
        </div>

        <!-- Game Grid Section -->
        <div class="grid justify-center items-center mt-6 w-full">
          <div :class="['grid', gridClasses, 'gap-2 sm:gap-4 lg:gap-6 p-2 sm:p-4', 'w-full']">
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
      </div>
    </CardCard>
  </div>
</template>
