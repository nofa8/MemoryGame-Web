<script setup>
const props = defineProps(['piece', 'index', 'isFlipped', 'isMatched'])
const emit = defineEmits(['flip'])

const flipCard = () => {
  // Emit the flip event only if the card is not already flipped or matched
  emit('flip', props.index)
}
</script>

<template>
  <div class="grow">
    <img
      v-if="props.isFlipped || props.isMatched"
      :src="`./cards/${props.piece}.png`"
      alt="Card image"
    />
    <img v-else @click="flipCard" :src="`/cards/semFace.png`" alt="Card back" />
  </div>
</template>
