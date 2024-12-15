<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const name = ref('');
const email = ref('');
const nickname = ref('');
const password = ref('');
const passwordConfirm = ref('');
const photo = ref(null);
const photoPreview = ref(null);
const errors = ref({});
const router = useRouter();

const validateForm = () => {
  errors.value = {}; // Reset previous errors
  let valid = true;

  // Validate Name
  if (!name.value) {
    errors.value.name = 'Name is required.';
    valid = false;
  } else if (name.value.length > 255) {
    errors.value.name = 'Name should be under 255 characters.';
    valid = false;
  }

  // Validate Email
  if (!email.value) {
    errors.value.email = 'Email is required.';
    valid = false;
  } else if (!/\S+@\S+\.\S+/.test(email.value)) {
    errors.value.email = 'Email is not valid.';
    valid = false;
  }

  // Validate Nickname
  if (!nickname.value) {
    errors.value.nickname = 'Nickname is required.';
    valid = false;
  } else if (nickname.value.length > 255) {
    errors.value.nickname = 'Nickname should be under 255 characters.';
    valid = false;
  }

  // Validate Password
  if (!password.value) {
    errors.value.password = 'Password is required.';
    valid = false;
  } else if (password.value.length < 3) {
    errors.value.password = 'Password must be at least 3 characters long.';
    valid = false;
  }

  // Validate Password Confirmation
  if (password.value !== passwordConfirm.value) {
    errors.value.passwordConfirm = 'Passwords do not match.';
    valid = false;
  }

  return valid;
};

const handlePhotoUpload = (e) => {
  const file = e.target.files[0];
  photo.value = file;
  if (file) {
    photoPreview.value = URL.createObjectURL(file);
  }
};

const register = async () => {
  if (validateForm()) {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('email', email.value);
    formData.append('nickname', nickname.value);
    formData.append('password', password.value);
    formData.append('password_confirmation', passwordConfirm.value);
    if (photo.value) {
      formData.append('photo', photo.value);
    }

    try {
      const response = await axios.post('/auth/register', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      // console.log('User registered successfully:', response.data);
      router.push('/login'); // Redirect to login page
    } catch (error) {
      if (error.response && error.response.data && error.response.data.errors) {
        errors.value = error.response.data.errors;
      } else {
        console.error('Unexpected error during registration:', error);
      }
    }
  }
};
</script>

<template>
  <div class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Register</h1>

    <form @submit.prevent="register" class="space-y-4">
      <!-- Name -->
      <div>
        <label for="name" class="block text-gray-700">Name</label>
        <input
          type="text"
          id="name"
          v-model="name"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
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
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
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
        <p v-if="errors.nickname" class="text-red-500 text-sm mt-1">{{ errors.nickname }}</p>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700">Password</label>
        <input
          type="password"
          id="password"
          v-model="password"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
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
        <p v-if="errors.passwordConfirm" class="text-red-500 text-sm mt-1">{{ errors.passwordConfirm }}</p>
      </div>

      <!-- Photo Upload -->
      <div>
        <label for="photo" class="block text-gray-700">Profile Photo (Optional)</label>
        <input
          type="file"
          id="photo"
          @change="handlePhotoUpload"
          class="w-full p-2 border border-gray-300 rounded-md"
        />
        <div v-if="photoPreview" class="mt-2">
          <img
            :src="photoPreview"
            alt="Profile Preview"
            class="w-24 h-24 rounded-full object-cover border"
          />
        </div>
      </div>

      <button
        type="submit"
        class="w-full py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
      >
        Register
      </button>
    </form>
  </div>
</template>
