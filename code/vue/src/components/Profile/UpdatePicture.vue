<script setup>
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import { ref } from 'vue'

const authStore = useAuthStore()

const photo = ref(null)
const photoPreview = ref(null)
const formError = ref('')
const emit = defineEmits()

const handlePhotoUpload = (e) => {
  const file = e.target.files[0]
  if (file) {
    photo.value = file
    photoPreview.value = URL.createObjectURL(file)
    formError.value = ''
  }
}

const updatePhoto = () => {
  const fileInput = document.querySelector('input[type="file"]')
  const file = fileInput.files[0]

  if (!file) {
    formError.value = 'No photo uploaded!'
    return
  }

  const formData = new FormData()
  formData.append('photo', file)

  axios({
    method: 'POST', // Use POST instead of PATCH
    url: '/users/image',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
    .then((response) => {
      authStore.setUser(response.data.user)
      formError.value = ''
      emit('picture-updated')
    })
    .catch((error) => {
      formError.value = 'An error occurred while uploading the photo.'
    })
}
</script>

<template>
  <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Change Profile Picture</h2>

    <div class="flex flex-col items-center space-y-4">
      <!-- File Input -->
      <label
        for="photo-upload"
        class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300"
      >
        <input id="photo-upload" type="file" @change="handlePhotoUpload" class="hidden" />
        Select Photo
      </label>

      <!-- Photo Preview -->
      <div v-if="photoPreview" class="flex justify-center mb-4">
        <img
          :src="photoPreview"
          alt="New Profile Preview"
          class="w-24 h-24 rounded-full object-cover border-2 border-gray-300"
        />
      </div>

      <!-- Error Message -->
      <p v-if="formError" class="text-red-500 text-sm text-center">{{ formError }}</p>
    </div>

    <!-- Buttons -->
    <div class="flex justify-center space-x-4 mt-6">
      <button
        @click="updatePhoto"
        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring focus:ring-green-300"
      >
        Update Picture
      </button>
      <button
        @click="$emit('hideChangePicture')"
        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring focus:ring-gray-300"
      >
        Cancel
      </button>
    </div>
  </div>
</template>
