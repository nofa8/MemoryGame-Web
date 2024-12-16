<script setup>
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import { computed, ref } from 'vue'
import DeleteUserAsAdmin from './DeleteUserAsAdmin.vue'

const authStore = useAuthStore()

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  photoUrl: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['userUpdated', 'userDeleted']) // Notify parent about user updates

// Local state to control the visibility of the delete form
const formDelete = ref(false)

// Function to block or unblock the user account
const blockAccount = async () => {
  try {
    const response = await axios.patch(`/users/${props.user.nickname}`)

    if (response.status === 200) {
      props.user.blocked = response.data.user.blocked // Update blocked status
    }
  } catch (err) {
    console.error('Failed to toggle the account status:', err)
    alert('There was an error toggling the account status. Please try again later.')
  }
}

// Function to restore a deleted account
const restoreAccount = async () => {
  try {
    const response = await axios.patch(`/auth/admin/${props.user.nickname}`)

    if (response.status === 200) {
      props.user.deleted_at = null // Restore the account by nullifying deleted_at
      emit('userUpdated', props.user)
      // Emit the updated user data
    }
  } catch (err) {
    console.error('Failed to restore the account:', err)
    alert('There was an error restoring the account. Please try again later.')
  }
}

// Show the delete form
const showDeleteForm = () => {
  formDelete.value = true
}

// Handle account deletion
const handleAccountDeleted = (updatedUser) => {
  props.user.deleted_at = updatedUser.deleted_at // Update the user state dynamically
  formDelete.value = false // Hide the delete form
}

const handleUserDeleted = (userId) => {
  // Handle the user deletion logic
  emit('userDeleted', userId)
}

// Hide the delete form
const hideDeleteForm = () => {
  formDelete.value = false
  emit('userUpdated') // Notify parent about form closure
}

// Computed property for user's full name
const fullName = computed(() => props.user.name)
</script>

<template>
  <div class="p-6 bg-white rounded-lg shadow-lg max-w-4xl mx-auto">
    <div class="flex items-center justify-between space-x-6">
      <!-- Profile Picture and User Info -->
      <div class="flex items-center space-x-6 flex-grow">
        <img
          :src="props.photoUrl"
          alt="Profile Picture"
          class="w-20 h-20 rounded-full object-cover border-2 border-gray-300"
        />
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ fullName }}</h2>
          <p class="text-sm text-gray-600">Nickname: {{ props.user.nickname }}</p>
          <p class="text-sm text-gray-600">Email: {{ props.user.email }}</p>
          <p class="text-sm text-gray-600">
            Type: {{ props.user.type === 'P' ? 'Player' : 'Admin' }}
          </p>
          <p v-if="props.user.type === 'P'" class="text-sm text-gray-600">
            Brain Coins: {{ props.user.brain_coins_balance }}
          </p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="props.user.nickname !== authStore.user.nickname" class="flex flex-col space-y-2">
        <!-- Remove Account Button -->
        <div>
          <button
            v-if="!props.user.deleted_at"
            @click="showDeleteForm"
            class="px-4 py-2 bg-red-500 text-white rounded-md"
          >
            Remove Account
          </button>
        </div>

        <!-- Block Account Button -->
        <button
          v-if="!props.user.blocked && !props.user.deleted_at"
          @click="blockAccount"
          class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
        >
          Block Account
        </button>

        <button
          v-else-if="props.user.blocked && !props.user.deleted_at"
          @click="blockAccount"
          class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Unblock Account
        </button>

        <!-- Restore Account Button -->
        <button
          v-if="props.user.deleted_at"
          @click="restoreAccount"
          class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
        >
          Restore Account
        </button>
      </div>
    </div>

    <!-- Delete Form Component -->
    <div v-if="formDelete" class="mt-6">
      <DeleteUserAsAdmin
        :user="props.user"
        @hidedeleteForm="hideDeleteForm"
        @accountDeleted="handleAccountDeleted"
        @userDeleted="handleUserDeleted"
      />
    </div>
  </div>
</template>
