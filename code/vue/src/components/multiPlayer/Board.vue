<script setup>
import Card from './Card.vue'

const props = defineProps({
  board: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['play'])

const playPieceOfBoard = (idx) => {
  emit('play', idx)
}
</script>

<template>
  <div class="grid grid-cols-3 border divide-y divide-x">
    <Card
      v-for="(piece, idx) in board"
      :key="idx"
      :piece="piece"
      :index="idx"
      @play="playPieceOfBoard"
    >
    </Card>
  </div>
</template>
