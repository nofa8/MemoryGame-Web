<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['piece', 'index', 'isFlipped', 'isMatched']);
const emit = defineEmits(['flip']);
// Audio for card flip
const flipSound = new Audio('/flip.mp3');
flipSound.preload = 'auto';

const flipCard = () => {
  // Emit the flip event only if the card is not already flipped or matched
  if (!props.isFlipped && !props.isMatched) {
    // flipSound.play(); // Play the flip sound
    emit('flip', props.index);
  }
}

watch(() => props.isFlipped, (newVal) => {
  if (newVal== true) {
    flipSound.play(); // Play the flip sound when the card is flipped
  }
})

const hidden = ref(false)

watch(()=> props.isMatched, (newVal) => {
  if (newVal == true){
    setTimeout(
       () => hidden.value = true, 1000
    )
  }
})
</script>

<template>
  <div class="card"  :style="{ visibility: hidden ? 'hidden' : 'visible' }">
    <div  @click="flipCard"
      :class="[
        'card-inner',
        { 'is-flipped': props.isFlipped || props.isMatched }
      ]"
    >
      <!-- Front of the card -->
      <div class="card-front">
        <img :src="`./cards/${props.piece}.png`" alt="Card image" />
      </div>

      <!-- Back of the card -->
      <div class="card-back">
        <img src="/cards/semFace.png" alt="Card back" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  perspective: 1000px; /* Enable 3D effect */
  width: 100px;
  height: 150px;
  cursor: pointer;
}

.card-inner {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  transition: transform 0.6s ease-in-out;
}

.card-inner.is-flipped {
  transform: rotateY(180deg); /* Flip the card */
}

.card-front,
.card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden; /* Hide the back side */
  border-radius: 8px; /* Optional: Add rounded corners */
}

.card-front {
  transform: rotateY(180deg); /* Make the front face hidden initially */
}

.card-back {
  background-color: #ccc; /* Optional: Customize back face color */
}
</style>
