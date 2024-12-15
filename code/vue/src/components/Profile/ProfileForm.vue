<script setup>
import { useAuthStore } from '@/stores/auth'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits()
const authStore = useAuthStore()
const name = ref('')
const email = ref('')
const nickname = ref('')
const password = ref('')
const passwordConfirm = ref('')
const formErrors = ref({
  passwordMatch: '',
  emailInvalid: '',
  passwordTooShort: '',
  nicknameTaken: '',
  name: '',
  email: '',
  nickname: '',
  password: '',
  passwordConfirm: ''
})

onMounted(() => {
  if (authStore.user) {
    name.value = authStore.user.name || ''
    email.value = authStore.user.email || ''
    nickname.value = authStore.user.nickname || ''
  }
})

const updateProfile = () => {
  if (validateForm()) {
    axios
      .put('/users/me', {
        name: name.value ? name.value : null,
        email: email.value ? email.value : null,
        nickname: nickname.value ? nickname.value : null,
        password: password.value ? password.value : null,
        password_confirmation: passwordConfirm.value ? passwordConfirm.value : null
      })
      .then((response) => {
        // console.log('Profile updated successfully:', response.data)
        authStore.setUser(response.data.user) // Update the local store with the new user data
      })
      .catch((error) => {
        console.error('Complete error object:', error)
        if (error.response) {
          // Safely access error.response.data
          console.error('Error updating profile:', error.response.data)
        } else {
          // Handle unexpected errors (e.g., network issues)
          console.error('Unexpected error:', error)
        }
      })
  } else {
    console.log('Form has errors')
  }
}

const validateForm = () => {
  formErrors.value = {
    // Clear previous errors
    passwordMatch: '',
    emailInvalid: '',
    passwordTooShort: '',
    nicknameTaken: '',
    name: '',
    email: '',
    nickname: '',
    password: '',
    passwordConfirm: ''
  }

  let valid = true

  // Validate Name
  if (name.value.length > 255) {
    formErrors.value.name = 'Name should be under 255 characters'
    valid = false
  }

  // Validate Email
  if (email.value && !/\S+@\S+\.\S+/.test(email.value)) {
    formErrors.value.email = 'Email is not valid'
    valid = false
  }

  // Validate Nickname
  if (nickname.value && nickname.value.length > 255) {
    formErrors.value.nickname = 'Nickname should be under 255 characters'
    valid = false
  }

  // Validate Password
  if (password.value && password.value.length < 3) {
    formErrors.value.password = 'Password must be at least 3 characters long'
    valid = false
  }

  // Validate Password Confirmation
  if (password.value && password.value !== passwordConfirm.value) {
    formErrors.value.passwordConfirm = 'Passwords do not match'
    valid = false
  }

  return valid
}

const backToProfile = () => {
  emit('back-to-profile') // Emit an event to go back to profile view
}
</script>

<template>
  <div class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Update Your Profile</h1>

    <div v-if="authStore.user" class="space-y-4">
      <!-- Name -->
      <div>
        <label for="name" class="block text-gray-700">Name</label>
        <input
          type="text"
          id="name"
          v-model="name"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="formErrors.name" class="text-red-500 text-sm">{{ formErrors.name }}</p>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700">Email</label>
        <input
          type="email"
          id="email"
          v-model="email"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="formErrors.email" class="text-red-500 text-sm">{{ formErrors.email }}</p>
      </div>

      <!-- Nickname -->
      <div>
        <label for="nickname" class="block text-gray-700">Nickname</label>
        <input
          type="text"
          id="nickname"
          v-model="nickname"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="formErrors.nickname" class="text-red-500 text-sm">{{ formErrors.nickname }}</p>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700">New Password</label>
        <input
          type="password"
          id="password"
          v-model="password"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="formErrors.password" class="text-red-500 text-sm">{{ formErrors.password }}</p>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="passwordConfirm" class="block text-gray-700">Confirm Password</label>
        <input
          type="password"
          id="passwordConfirm"
          v-model="passwordConfirm"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="formErrors.passwordConfirm" class="text-red-500 text-sm">
          {{ formErrors.passwordConfirm }}
        </p>
      </div>

      <div class="mt-4">
        <button
          @click="updateProfile"
          class="w-full py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
        >
          Update Profile
        </button>
      </div>

      <div class="mt-4">
        <button
          @click="backToProfile"
          class="w-full py-2 bg-white-500 text-black rounded-md hover:bg-white-600"
        >
          Back
        </button>
      </div>
    </div>
  </div>
</template>
