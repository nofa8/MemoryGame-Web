<script setup>
import { watch } from 'vue'
import Cell from './Cell.vue'
import { useTicTacToe } from './tictactoe.js'

// these properties and methods are defined on the composable useTicTacToe
// but can be used as if they were "directly" defined on this component
// Note that the currentPlayer from the composable was "renamed" to player
const {
    status,
    currentPlayer: player,
    board,
    start,
    play
} = useTicTacToe()

const emit = defineEmits(['playerChanged', 'statusChanged'])

const playPieceOfBoard = (idx) => {
    play(idx)
}

watch(
    player,
    (newValue) => {
        emit('playerChanged', newValue)
    }
)

watch(
    status,
    (newValue) => {
        emit('statusChanged', newValue)
    }
)

defineExpose({
    start
})
</script>

<template>
    <div class="grid grid-cols-3 border divide-y divide-x">
        <Cell v-for="(piece, idx) in board" :key="idx" 
                :piece="piece" :index="idx" 
                @play="playPieceOfBoard">
        </Cell>
    </div>
</template>
