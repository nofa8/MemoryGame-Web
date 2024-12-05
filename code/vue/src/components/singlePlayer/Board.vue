<script setup>
import { ref, computed, watch } from 'vue'
import Card from './Card.vue'
import { inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Card as CardCard } from '@/components/ui/card'
import { useStopwatch } from 'vue-timer-hook'
import { useAuthStore } from '@/stores/auth'
import { useErrorStore } from '@/stores/error'
import axios from 'axios'
import { useMemoryGame } from './memory'

const storeAuth = useAuthStore()
const storeError = useErrorStore()

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

const { status, board, moves, matchedPairs, start, play, gameEnded, currentPlayer } = useMemoryGame(
  rows,
  cols
)

const alertDialog = inject('alertDialog')
const autoStart = true
const stopwatch = useStopwatch(autoStart)

const showSeconds = computed(() => {
  return stopwatch.seconds.value + 60 * stopwatch.minutes.value
})
const flip = (index) => {
  play(index)
}
watch(gameEnded, (newValue) => {
  if (newValue === true) {
    stopwatch.pause()
    alertDialog.value.open(
      goToGamehistory,
      'Are you sure?',
      'Cancel',
      `Yes, delete task #` + showSeconds.value,
      `This action cannot be undone. This will permanently delete the task 
        " from our servers.`
    )

    if (storeAuth.user != null) {
      try {
        /*const payload = {
        created_user_id: storeAuth.user.id,
        type: 'S',
        board_id: board.id,
        status: 'E'
        };*/
        const payload = {
          status: 'E'
        }
        console.log(game_id.value)
        const response = axios.put(`/games/${game_id.value}`, payload).then((response) => {
          //console.log(response.data.data)
          //fazer update das coins visualmente
          router.push({ name: 'gameMode' })
          /* fazer aqui o depois
            
            isLoading.value = false
            return response*/
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

watch(gameInterrupted, (newValue) => {
  if (newValue === true) {
    stopwatch.pause()
    alertDialog.value.open(
      goToGamehistory,
      'Are you sure?',
      'Cancel',
      `Yes, delete task #` + showSeconds.value,
      `PARASTE DE JOGAR WTF MATE 
        " from our servers.`
    )

    if (storeAuth.user != null) {
      try {
        /*const payload = {
        created_user_id: storeAuth.user.id,
        type: 'S',
        board_id: board.id,
        status: 'E'
        };*/
        const payload = {
          status: 'I'
        }
        console.log(game_id.value)
        const response = axios.put(`/games/${game_id.value}`, payload).then((response) => {
          //console.log(response.data.data)
          //fazer update das coins visualmente
          router.push({ name: 'gameMode' })
          /* fazer aqui o depois
       
            isLoading.value = false
            return response*/
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

const gridClasses = computed(() => `grid grid-cols-[${props.rows}] gap-2`)
</script>

<!-- <script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Card from './Card.vue'
import { useMemoryGame } from './memory'
import { Card as CardCard } from '@/components/ui/card'

const props = defineProps({
  rows: {
    type: Number,
    default: 3
  },
  cols: {
    type: Number,
    default: 4
  }
})



const emit = defineEmits(['statusChanged'])

const gridClasses = computed(() => `grid grid-cols-[${props.cols}] gap-2`)

const flip = (index) => {
  play(index)
}
watch(status, (newValue) => {
  emit('statusChanged', newValue)
})

defineExpose({
  start
})
</script> -->

<template>
  <div class="flex justify-center items-center p-4">
    <CardCard class="max-w-6xl h-auto rounded-lg bg-white dark:bg-gray-800 border-0 shadow-md p-4">
      <div class="text-center">
        <p class="text-black dark:text-white mb-4 text-xl">Game</p>
        <p class="text-black dark:text-white mb-4 text-xl">{{ showSeconds }}</p>
        <div class="flex items-center gap-2">
          <div :class="gridClasses" class="border">
            <Card
              v-for="(card, idx) in board"
              :key="idx"
              :piece="card.value"
              :index="card.index"
              :is-flipped="card.isFlipped"
              :is-matched="card.isMatched"
              @flip="flip"
            >
            </Card>
          </div>
        </div>
      </div>
    </CardCard>
  </div>
</template>
