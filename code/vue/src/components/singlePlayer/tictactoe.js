// tictactoe game engine as a Composable
import { ref } from 'vue'

// by convention, composable function names start with "use"
export function useTicTacToe() {

    // state encapsulated and managed by the composable:
    const status = ref(null)
    // null -> game has not ended
    // 1 -> player 1 is the winner
    // 2 -> player 2 is the winner
    // 3 -> draw
    let currentPlayer = ref(1)
    let board = ref([0, 0, 0, 0, 0, 0, 0, 0, 0])

    // start or restart the game 
    const start = () => {
        status.value = null
        // null -> game has not ended
        // 1 -> player 1 is the winner
        // 2 -> player 2 is the winner
        // 3 -> draw
        currentPlayer.value = 1
        board.value = [0, 0, 0, 0, 0, 0, 0, 0, 0]
    }

    const hasFullLine = (playerNumber) =>
        ((board.value[0] === playerNumber) && (board.value[1] === playerNumber) && (board.value[2] === playerNumber)) ||
        ((board.value[3] === playerNumber) && (board.value[4] === playerNumber) && (board.value[5] === playerNumber)) ||
        ((board.value[6] === playerNumber) && (board.value[7] === playerNumber) && (board.value[8] === playerNumber)) ||
        ((board.value[0] === playerNumber) && (board.value[3] === playerNumber) && (board.value[6] === playerNumber)) ||
        ((board.value[1] === playerNumber) && (board.value[4] === playerNumber) && (board.value[7] === playerNumber)) ||
        ((board.value[2] === playerNumber) && (board.value[5] === playerNumber) && (board.value[8] === playerNumber)) ||
        ((board.value[0] === playerNumber) && (board.value[4] === playerNumber) && (board.value[8] === playerNumber)) ||
        ((board.value[2] === playerNumber) && (board.value[4] === playerNumber) && (board.value[6] === playerNumber))

    const changeGameStatus = () => {
        if (hasFullLine(1)) {
            status.value = 1
        } else if (hasFullLine(2)) {
            status.value = 2
        } else if (isBoardComplete()) {
            status.value = 3
        } else {
            status.value = null
        }            
    }

    const isBoardComplete = () => board.value.findIndex(piece => piece === 0) < 0

    // Plays a specific piece of the game (defined by its index)
    // return true for a valid play; false for an invalid play
    const play = (index) => {
        if (!gameEnded()) {
            if (board.value[index] === 0) {
                board.value[index] = currentPlayer.value
                currentPlayer.value = currentPlayer.value === 1 ? 2 : 1
                changeGameStatus()
                return true
            }
        }
        return false
    }

    // returns whether the game as ended or not
    const gameEnded = () => status.value !== null

    // expose managed state and methods as return value
    // These properties and methods will be "directly" available on 
    // the component that uses this composable 
    return {
        status,
        currentPlayer,
        board,
        start, 
        play
    }
}