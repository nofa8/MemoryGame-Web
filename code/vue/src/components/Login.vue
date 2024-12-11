<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router'; // For navigation
import { useErrorStore } from '@/stores/error';
import ErrorMessage from './common/ErrorMessage.vue';

const authStore = useAuthStore();
const storeError = useErrorStore();
const router = useRouter();

const email = ref('');
const password = ref('');
const responseData = ref('');

const submit = async () => {
  try {
    const user = await authStore.login({
      email: email.value,
      password: password.value,
    });
    responseData.value = user.name;
  } catch (error) {
    console.error('Login failed:', error);
  }
};

const goToRegister = () => {
  router.push('/register'); // Navigate to the register component
};
</script>

<template>
  <div class="max-w-2xl mx-auto py-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Login</h2>
    <form class="space-y-6">
      <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-gray-700"> Email: </label>
        <input
          type="email"
          id="email"
          v-model="email"
          class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        />
        <ErrorMessage :errorMessage="storeError.fieldMessage('email')"></ErrorMessage>
      </div>

      <div class="space-y-2">
        <label for="password" class="block text-sm font-medium text-gray-700"> Password: </label>
        <input
          type="password"
          id="password"
          v-model="password"
          class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        />
        <ErrorMessage :errorMessage="storeError.fieldMessage('password')"></ErrorMessage>
      </div>

      <div class="flex space-x-4">
        <!-- Login Button -->
        <button
          @click.prevent="submit"
          type="submit"
          class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm"
        >
          Login
        </button>

        <!-- Register Button -->
        <button
          @click.prevent="goToRegister"
          type="button"
          class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm"
        >
          Register
        </button>
      </div>

      <div v-if="responseData" class="space-y-2 mt-8">
        <label for="response" class="block text-sm font-medium text-gray-700"> Response </label>
        <textarea
          :value="responseData"
          id="response"
          rows="3"
          class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          readonly
        ></textarea>
      </div>
    </form>
  </div>
</template> 