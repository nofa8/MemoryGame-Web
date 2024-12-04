<script setup>
const props = defineProps(['piece', 'index', 'isFlipped', 'isMatched'])
const emit = defineEmits(['flip'])
console.log(props.piece)
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
      class="w-24 h-32 object-cover rounded-lg"
    />

    <img
      v-else
      @click="flipCard"
      :src="`/cards/semFace.png`"
      alt="Card back"
      class="w-24 h-32 object-cover rounded-lg"
    />
  </div>
</template>
