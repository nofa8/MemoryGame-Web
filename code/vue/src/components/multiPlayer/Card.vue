<!-- <script setup>
const props = defineProps(['piece', 'index'])
const emit = defineEmits(['playPieceOfBoard'])

const play = () => {
  emit('playPieceOfBoard', props.index)
}
</script>

<template>
  <div class="grow">
    <img
      :src="'img/' + piece + '.png'"
      alt="Tictactoe piece"
      class="w-full h-full"
      @click.prevent="play"
    />
  </div>
</template> -->

<script setup>
const props = defineProps(['piece', 'index', 'isFlipped', 'isMatched'])
const emit = defineEmits(['flip'])

const flipCard = () => {
  // Emit the flip event only if the card is not already flipped or matched
  if (!props.isFlipped && !props.isMatched) {
    emit('flip', props.index)
  }
}
</script>

<template>
  <div class="grow">
    <div
      class="relative w-full h-full bg-gray-200 rounded-lg shadow-lg flex items-center justify-center cursor-pointer"
      @click="flipCard"
    >
      <img
        v-if="isFlipped || isMatched"
        :src="`/cards/${piece}.png`"
        alt="Card image"
        class="w-full h-full object-cover rounded-lg"
      />
      <div
        v-else
        class="absolute inset-0 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-xl"
      >
        ?
      </div>
    </div>
  </div>
</template>
