<script setup>
import { onMounted } from 'vue'
import Game from './Game.vue'
import { useMultiplayerGamesStore } from '@/stores/multiplayer';


const storeMultiGames = useMultiplayerGamesStore()

onMounted(() => {
    storeMultiGames.fetchPlayingGames()
})
</script>

<template>
    <div class="flex flex-wrap justify-stretch">
        <Game v-for="game in storeMultiGames.games" :key="game.id" :game="game">
        </Game>
    </div>
</template>