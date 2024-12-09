import { ref, computed } from 'vue'

export function useMemoryGame(board_rows = 4, board_cols = 3) {
  const boardSize = board_cols * board_rows
  // Game status
  const status = ref(0)
  // Game underway -> 0
  // Ended -> 1
  const board = ref([])
  const flippedCards = ref([])
  const matchedPairs = ref([])
  const moves = ref(0)
  const streak = ref(0) // Track current streak
  const bestStreak = ref(0) // Track the best streak
  const correctSound = new Audio('/correct.mp3')
  const wrongSound = new Audio('/wrong.mp3')

  correctSound.preload = 'auto'
  wrongSound.preload = 'auto'

  // Create card pairs
  const createCardPairs = () => {
    const cardTypes = ['e', 'o', 'p', 'c']
    const cardValues = [1, 2, 3, 4, 5, 6, 7, 11, 12, 13]

    const cardCombinations = cardTypes.flatMap((type) =>
      cardValues.map((value) => `${type}${value}`)
    )

    const selectedCombinations = cardCombinations
      .sort(() => Math.random() - 0.5)
      .slice(0, boardSize / 2)

    const shuffledCards = [...selectedCombinations, ...selectedCombinations]
      .sort(() => Math.random() - 0.5)
      .map((cardCode, index) => ({
        id: index,
        value: cardCode,
        isFlipped: false,
        isMatched: false
      }))

    return shuffledCards
  }

  // Start or restart the game
  const start = () => {
    status.value = '0'
    board.value = createCardPairs()
    flippedCards.value = []
    matchedPairs.value = []
    moves.value = 0
    streak.value = 0
    bestStreak.value = 0
  }

  // Flip a card
  const play = (index) => {
    if (status.value === '1' || board.value[index].isMatched || flippedCards.value.length >= 2) {
      return false
    }

    board.value[index].isFlipped = true
    flippedCards.value.push(board.value[index])

    if (flippedCards.value.length === 2) {
      moves.value++

      if (flippedCards.value[0].value === flippedCards.value[1].value) {
        board.value.forEach((card) => {
          if (flippedCards.value.some((f) => f.id === card.id)) {
            card.isMatched = true
          }
        })

        matchedPairs.value.push(flippedCards.value[0].value)
        flippedCards.value = []

        streak.value++ // Increment streak on correct match
        if (streak.value > bestStreak.value) {
          bestStreak.value = streak.value // Update best streak if necessary
        }

        correctSound.play()
      } else {
        setTimeout(() => {
          wrongSound.play()
          flippedCards.value.forEach((card) => {
            if (!card.isMatched) {
              card.isFlipped = false
            }
          })
          flippedCards.value = []

          streak.value = 0 // Reset streak on incorrect match
        }, 1000)
      }
    }

    changeGameStatus()
    return true
  }

  // Change game status
  const changeGameStatus = () => {
    if (matchedPairs.value.length === boardSize / 2) {
      status.value = '1' // Game ended
    }
  }

  // Expose managed state and methods
  return {
    status,
    board,
    moves,
    matchedPairs,
    streak,
    bestStreak,
    start,
    play
  }
}
