<script setup>
import { inject, provide, useTemplateRef } from 'vue'
import Toaster from './components/ui/toast/Toaster.vue'
import { useAuthStore } from './stores/auth'
import { useChatStore } from './stores/chat'
import GlobalAlertDialog from './components/common/GlobalAlertDialog.vue'
import GlobalInputDialog from './components/common/GlobalInputDialog.vue'

const storeAuth = useAuthStore()
const storeChat = useChatStore()
const socket = inject('socket')

const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

const inputDialog = useTemplateRef('input-dialog')
provide('inputDialog', inputDialog)

const logoutConfirmed = () => {
  storeAuth.logout()
}

const logout = () => {
  alertDialog.value.open(
    logoutConfirmed,
    'Logout Confirmation',
    'Cancel',
    'Yes, Log Out',
    `Are you sure you want to log out? You can log back in later using your credentials.`
  )
}

let userDestination = null
socket.on('privateMessage', (messageObj) => {
  userDestination = messageObj.user
  inputDialog.value.open(
    handleMessageFromInputDialog,
    `Message from ${messageObj.user.name}`,
    `You received a private message from ${messageObj.user.name}.`,
    'Reply',
    '',
    'Close',
    'Reply',
    messageObj.message
  )
})

const handleMessageFromInputDialog = (message) => {
  storeChat.sendPrivateMessageToUser(userDestination, message)
}
</script>

<template>
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>
  <GlobalInputDialog ref="input-dialog"></GlobalInputDialog>

  <div class="p-8 mx-auto max-w-7xl min-h-screen space-y-6">
    <!-- Header -->
    <header
      class="flex items-center justify-between py-4 px-6 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white rounded-lg shadow-lg"
    >
      <div class="flex items-center space-x-4">
        <img src="/logo.png" alt="Memory Game Logo" class="w-12 h-12 rounded-full shadow-md" />
        <h1 class="text-3xl font-bold tracking-wide">Memory Game</h1>
      </div>
      <p class="text-lg">
        {{
          storeAuth.user ? 'Welcome, ' + storeAuth.userFirstLastName : 'Ready to test your memory?'
        }}
      </p>
    </header>

    <!-- Navigation -->
    <nav class="flex items-center justify-between bg-gray-100 rounded-lg shadow p-4">
      <div class="flex items-center space-x-6">
        <RouterLink
          :to="{ name: 'singlePlayerGames' }"
          class="px-6 py-3 rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-md"
          active-class="bg-blue-800 hover:bg-blue-800"
        >
          Single Player
        </RouterLink>
        <RouterLink
          :to="{ name: 'multiPlayerGames' }"
          class="px-6 py-3 rounded-md text-white bg-green-600 hover:bg-green-700 transition-all shadow-md"
          active-class="bg-green-800 hover:bg-green-800"
        >
          Multi Player
        </RouterLink>
        <RouterLink
          :to="{ name: 'multiPlayerGames' }"
          class="px-6 py-3 rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-md"
          active-class="bg-purple-800 hover:bg-purple-800"
        >
          Profile
        </RouterLink>
        <RouterLink
          :to="{ name: 'history' }"
          class="px-6 py-3 rounded-md text-white bg-red-600 hover:bg-red-700 transition-all shadow-md"
          active-class="bg-red-800 hover:bg-red-800"
        >
          History
        </RouterLink>
      </div>

      <div class="flex items-center space-x-4">
        <RouterLink
          v-show="!storeAuth.user"
          :to="{ name: 'login' }"
          class="px-6 py-3 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-md"
          active-class="bg-indigo-800 hover:bg-indigo-800"
        >
          Login
        </RouterLink>
        <button
          v-show="storeAuth.user"
          @click="logout"
          class="px-6 py-3 rounded-md text-white bg-red-600 hover:bg-red-700 transition-all shadow-md"
        >
          Logout
        </button>
      </div>
    </nav>

    <!-- Content -->
    <main class="bg-white rounded-lg shadow p-6">
      <RouterView></RouterView>
    </main>
  </div>
</template>

<style>
body {
  background: linear-gradient(to bottom, #f0f4f8, #cfd9df);
  color: #1f2937;
  font-family: 'Inter', sans-serif;
}
</style>
