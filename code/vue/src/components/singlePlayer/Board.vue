<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Card from './Card.vue'
import { useMemoryGame } from './memory'

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

const { status, board, moves, matchedPairs, start, play, gameEnded } = useMemoryGame(
  props.cols * props.rows
)

const emit = defineEmits(['statusChanged'])

const gridClasses = computed(() => `grid grid-cols-[${props.rows}] gap-2`)

const flip = (index) => {
  play(index)
}
watch(status, (newValue) => {
  emit('statusChanged', newValue)
})

defineExpose({
  start
})
</script>

<template>
  <div :class="gridClasses" class="border">
    <Card
      v-for="(card, idx) in board"
      :key="idx"
      :piece="card.value"
      :index="idx"
      :is-flipped="card.isFlipped"
      :is-matched="card.isMatched"
      @flip="flip"
    >
    </Card>
  </div>
</template>
