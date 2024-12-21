<script setup>
import Lobby from './Lobby.vue'
import Games from './Games.vue'
import Chat from '@/components/chat/Chat.vue'
import { useAuthStore } from '@/stores/auth'

const storeAuth = useAuthStore()
</script>
<template>
  <div class="flex flex-wrap justify-start space-x-4">
    <Chat class="grow"></Chat>
    <Lobby v-if="storeAuth.user" class="grow"></Lobby>
  </div>
  <div class="flex flex-wrap justify-start space-x-4">
    <Games v-if="storeAuth.user" class="flex-1 min-w-96"></Games>
  </div>
</template>
