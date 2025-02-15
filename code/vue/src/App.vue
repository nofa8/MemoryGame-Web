<script setup>
import { computed, inject, onMounted, provide, ref, useTemplateRef } from 'vue'
import Toaster from './components/ui/toast/Toaster.vue'
import { useAuthStore } from './stores/auth'
import { useChatStore } from './stores/chat'
import GlobalAlertDialog from './components/common/GlobalAlertDialog.vue'
import GlobalInputDialog from './components/common/GlobalInputDialog.vue'
import router from './router'

const storeAuth = useAuthStore()
const storeChat = useChatStore()
const socket = inject('socket')
const isDropdownOpen = ref(false)

const ambientSound = new Audio('/ambient-timestrech.mp3')
ambientSound.volume = 0.3
ambientSound.loop = true
const isMusicPlaying = ref(false)

const firstTime = ref(true)

const toggleMusic = () => {
  if (isMusicPlaying.value) {
    ambientSound.pause()
  } else {
    ambientSound.play()
  }
  isMusicPlaying.value = !isMusicPlaying.value // Toggle the state
}

const alertDialog = useTemplateRef('alert-dialog')
provide('alertDialog', alertDialog)

const inputDialog = useTemplateRef('input-dialog')
provide('inputDialog', inputDialog)

const logoutConfirmed = () => {
  router.push({ name: 'singlePlayerGames' })
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

onMounted(() => {
  document.addEventListener('click', () => {
    // Play the ambient sound only if it isn't already playing
    if (firstTime.value && !isMusicPlaying.value) {
      ambientSound.play()
      firstTime.value = false
      isMusicPlaying.value = true
      console.log('Ambient music started!')
    }
  })
})

const authenticated = computed(() => {
  return storeAuth.user != null
})
const isAdmin = computed(() => {
  return storeAuth.user != null ? (storeAuth.user.type == 'A' ? true : false) : false
})
</script>

<template>
  <Toaster />
  <GlobalAlertDialog ref="alert-dialog"></GlobalAlertDialog>

  <GlobalInputDialog ref="input-dialog"></GlobalInputDialog>

  <div class="p-4 sm:p-8 mx-auto max-w-full lg:max-w-7xl min-h-screen space-y-6">
    <!-- Header -->
    <header
      class="flex items-center justify-between py-4 px-6 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 text-white rounded-lg shadow-lg"
    >
      <div class="flex items-center space-x-4">
        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
          <img src="/logo.png" alt="Memory Game Logo" class="w-12 h-12 rounded-full shadow-md" />
          <h1 class="text-xl sm:text-3xl font-bold tracking-wide text-center sm:text-left">
            Memory Game
          </h1>
        </div>
      </div>
      <!-- Music Icon -->
      <button
        v-show="!firstTime"
        @click="toggleMusic"
        class="flex items-center space-x-2 text-white"
      >
        <span v-if="!isMusicPlaying">
          <!-- Playing Icon -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="w-6 h-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            />
          </svg>
        </span>
        <span v-else>
          <!-- Pause Icon -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="w-6 h-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </span>
        <span>Music</span>
      </button>
      <p class="text-sm sm:text-lg text-center sm:text-right">
        <span v-if="storeAuth.user">
          {{ storeAuth.userFirstLastName }}. -> {{ storeAuth.userbrain_coins_balance }} Brain Coins
          <img
            v-if="storeAuth.userPhotoUrl"
            :src="storeAuth.userPhotoUrl"
            alt="User Photo"
            class="rounded-full w-8 h-8 ml-2 inline-block"
          />
        </span>
        <span v-else> Ready to test your memory? </span>
      </p>
    </header>

    <!-- Navigation -->

    <nav
      class="flex flex-col sm:flex-row items-center justify-between bg-gray-100 rounded-lg shadow p-4 space-y-4 sm:space-y-0"
    >
      <div
        class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6"
      >
        <RouterLink
          v-if="!isAdmin"
          :to="{ name: 'singlePlayerGames' }"
          class="w-full sm:w-auto px-4 py-2 text-center rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-md"
          active-class="bg-blue-800 hover:bg-blue-800"
        >
          Single Player
        </RouterLink>
        <RouterLink
          v-if="!isAdmin"
          :to="{ name: 'multiPlayerGames' }"
          class="w-full sm:w-auto px-4 py-2 text-center rounded-md text-white bg-green-600 hover:bg-green-700 transition-all shadow-md"
          active-class="bg-green-800 hover:bg-green-800"
        >
          Multi Player
        </RouterLink>
        <RouterLink
          :to="{ name: 'profile' }"
          class="w-full sm:w-auto px-4 py-2 text-center rounded-md text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-md"
          active-class="bg-purple-800 hover:bg-purple-800"
        >
          Profile
        </RouterLink>
        <RouterLink
          v-if="authenticated"
          :to="{ name: 'history' }"
          class="px-6 py-3 rounded-md text-white bg-red-600 hover:bg-red-700 transition-all shadow-md"
          active-class="bg-red-800 hover:bg-red-800"
        >
          History
        </RouterLink>

        <div class="relative">
          <button
            @click="(isDropdownOpen = !isDropdownOpen)"
            class="px-6 py-3 rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-all shadow-md"
          >
            Scoreboards
            <span class="ml-2">▼</span>
          </button>

          <div
            v-if="isDropdownOpen"
            class="absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-orange-100 ring-1 ring-orange-200"
          >
            <div class="py-1">
              <router-link
                v-if="authenticated && !isAdmin"
                :to="{ name: 'scoreboardPersonal' }"
                class="block px-4 py-2 text-sm text-orange-800 hover:bg-orange-200 transition-colors"
                @click="(isDropdownOpen = false)"
              >
                Personal Scoreboard
              </router-link>
              <router-link
                :to="{ name: 'scoreboardGlobal' }"
                class="block px-4 py-2 text-sm text-orange-800 hover:bg-orange-200 transition-colors"
                @click="(isDropdownOpen = false)"
              >
                Global Scoreboard
              </router-link>
            </div>
          </div>
        </div>
        <RouterLink
          v-if="authenticated"
          :to="{ name: 'transactions' }"
          class="px-6 py-3 rounded-md text-white bg-yellow-500 hover:bg-pink-500 transition-all shadow-md"
          active-class="bg-pink-600 hover:bg-pink-600"
        >
          Transactions
        </RouterLink>
        <RouterLink
          :to="{ name: 'statistics' }"
          class="px-6 py-3 rounded-md text-white bg-teal-500 hover:bg-teal-600 transition-all shadow-md"
          active-class="bg-teal-700 hover:bg-teal-700"
        >
          Statistics
        </RouterLink>
      </div>
      <div
        class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4"
      >
        <RouterLink
          v-show="!authenticated"
          :to="{ name: 'login' }"
          class="w-full sm:w-auto px-4 py-2 text-center rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-md"
          active-class="bg-indigo-800 hover:bg-indigo-800"
        >
          Login
        </RouterLink>
        <button
          v-show="storeAuth.user"
          @click="logout"
          class="w-full sm:w-auto px-4 py-2 text-center rounded-md text-white bg-red-600 hover:bg-red-700 transition-all shadow-md"
        >
          Logout
        </button>
      </div>
    </nav>

    <!-- Content -->
    <main class="bg-white rounded-lg shadow p-4 sm:p-6">
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
