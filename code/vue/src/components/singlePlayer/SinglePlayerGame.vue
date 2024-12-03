<script setup>
import { ref, computed, onMounted } from 'vue'
import Board from './Board.vue'

const board = ref(null)

const currentPlayer = ref(1)
const currentStatus = ref(null)

const statusChanged = (newStatus) => {
  currentStatus.value = newStatus
}
const playerChanged = (newPlayer) => {
  currentPlayer.value = newPlayer
}

const currentPlayerName = computed(() => {
  return currentPlayer.value === 1 ? 'First Player (cross)' : 'Second Player (circle)'
})

const statusGameMessage = computed(() => {
  switch (currentStatus.value) {
    case 1:
      return 'First Player (cross) won the Game'
    case 2:
      return 'Second Player (circle) won the Game'
    case 3:
      return 'The Game ended in a Draw'
    default:
      return 'Game has not ended yet!'
  }
})

const restart = () => {
  board.value.start()
}

onMounted(() => {
  restart()
})
</script>


<template>
  <div class="p-8 mx-auto max-w-3xl min-w-96">
    <h1 class="text-3xl pb-2">Single Player</h1>
    <hr>
    <div>
      <div class="py-4">
        <p class="text-lg">Current Player : <strong>{{ currentPlayerName }}</strong></p>
      </div>
      <div>
          <div v-show="currentStatus" class="my-4 p-3 flex items-center bg-green-200 rounded-xl">
            <div class="grow flex-col">
              <p class="text-center text-xl font-bold">Game ended!</p>             
              <p class="text-center text-lg">{{ statusGameMessage }}</p>
            </div>          
            <button type="button" class="rounded bg-green-700 py-2 px-3 m-0.5 text-white flex shadow-lg shadow-green-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span class="ps-2 text-base" @click="restart">Restart</span>
            </button>           
          </div>
          <Board ref="board" @playerChanged="playerChanged" @statusChanged="statusChanged"></Board>
      </div>
    </div>
  </div>
</template>