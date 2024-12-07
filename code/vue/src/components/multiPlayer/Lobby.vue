<script setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { ref, onMounted, computed } from 'vue'
import ListGamesLobby from './ListGamesLobby.vue'
import { useLobbyStore } from '@/stores/lobby'
import { useBoardsStore } from '@/stores/board'
import AlertDialog from '../ui/alert-dialog/AlertDialog.vue'

const storeLobby = useLobbyStore()
const storeBoard = useBoardsStore()

const boards = computed(() => storeBoard.boards)
const selectedBoard = ref(null) 

// Fetch boards and lobby games on component mount
onMounted(() => {
  storeBoard.loadBoards()
  storeLobby.fetchGames()
})

// Handle creating a new game with the selected board
const addNewGame = () => {
  if (!selectedBoard.value) {
    alert('Please select a board before creating a game.')
    return
  }
  storeLobby.addGame(selectedBoard.value) 
}
</script>

<template>
  <Card class="my-8 py-2 px-1">
    <CardHeader class="pb-0">
      <CardTitle>Lobby</CardTitle>
      <CardDescription>
        {{ storeLobby.totalGames === 1 ? '1 game' : storeLobby.totalGames + ' games' }} waiting.
      </CardDescription>
    </CardHeader>
    <CardContent class="p-4">
      <!-- Board Selection Dropdown -->
      <div class="py-2">
        <label for="board-select" class="block mb-2">Select Board:</label>
        <select v-model="selectedBoard" id="board-select" class="p-2 border rounded w-full">
          <option disabled value="">Choose a board</option>
          <option v-for="board in boards" :key="board.id" :value="board">
            {{ board.cols }}x{{ board.rows }}
          </option>
        </select>
      </div>
      <!-- Add Game Button -->
      <div class="py-2">
        <Button @click="addNewGame"> New Game </Button>
      </div>
      <!-- List of Games -->
      <div v-if="storeLobby.totalGames > 0">
        <ListGamesLobby></ListGamesLobby>
      </div>
      <div v-else>
        <h2 class="text-xl">The lobby is empty!</h2>
      </div>
    </CardContent>
  </Card>
</template>
