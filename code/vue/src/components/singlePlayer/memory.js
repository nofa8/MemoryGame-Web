import { ref, computed } from 'vue'

export function useMemoryGame(board_rows = 4, board_cols = 3) {
  const boardSize = board_cols * board_rows
  // Game status
  const status = ref(0)
  // Game under way -> 0
  // Ended-> 1
  const board = ref([])
  // let currentPlayer = ref(1)
  const flippedCards = ref([])
  const matchedPairs = ref([])
  const moves = ref(0)

  // Create card pairs
  const createCardPairs = () => {
    // Define possible card types and values
    const cardTypes = ['e', 'o', 'p', 'c']
    const cardValues = [1, 2, 3, 4, 5, 6, 7, 11, 12, 13]

    // Generate all possible unique card combinations
    const cardCombinations = cardTypes.flatMap((type) =>
      cardValues.map((value) => `${type}${value}`)
    )

    // Randomly select half the board size of unique combinations
    const selectedCombinations = cardCombinations
      .sort(() => Math.random() - 0.5)
      .slice(0, boardSize / 2)

    // Duplicate values and create card objects
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
  }

  // Flip a card
  const play = (index) => {
    if (status.value === '1' || board.value[index].isMatched || flippedCards.value.length >= 2) {
      return false
    }

    // Flip the card
    board.value[index].isFlipped = true
    flippedCards.value.push(board.value[index])

    // Check for match when two cards are flipped
    if (flippedCards.value.length === 2) {
      moves.value++

      // If cards match
      if (flippedCards.value[0].value === flippedCards.value[1].value) {
        // Mark cards as matched
        board.value.forEach((card) => {
          if (flippedCards.value.some((f) => f.id === card.id)) {
            card.isMatched = true
          }
        })

        // Add to matched pairs
        matchedPairs.value.push(flippedCards.value[0].value)
        flippedCards.value = []
      } else {
        // Reset flipped cards after a delay if not matched
        setTimeout(() => {
          flippedCards.value.forEach((card) => {
            if (!card.isMatched) {
              card.isFlipped = false
            }
          })
          flippedCards.value = []
        }, 1000)
      }
    }

    // Check game completion
    changeGameStatus()

    return true
  }

  // Change game status
  const changeGameStatus = () => {
    // Check if all pairs are matched
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
    start,
    play
  }
}
