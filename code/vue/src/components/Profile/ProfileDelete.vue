<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
const AuthStore = useAuthStore()
// Props and emits
defineProps(['user']);
const emit = defineEmits(['hidedeleteForm']);

const password = ref('');
const errorMessage = ref('');
const isSubmitting = ref(false);


const handleDelete = () => {
    if (!password.value) {
        errorMessage.value = 'Please enter your password to confirm deletion.';
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    axios
        .delete('/users/me', {
            data: {
                password: password.value,
            },
        })
        .then(() => {
            console.log('Profile deleted successfully.');
            // Optionally redirect or show a success message
        })
        .catch((error) => {
            if (error.response && error.response.data) {
                errorMessage.value = error.response.data.message || 'Failed to delete the profile.';
            } else {
                errorMessage.value = 'An unexpected error occurred.';
            }
        })
        .finally(() => {
            isSubmitting.value = false;
        });
        AuthStore.logout()

};

const handleCancel = () => {
    emit('hidedeleteForm'); // Emit event to hide the delete form
};
</script>

<template>
    <div class="max-w-lg mx-auto p-6 bg-red-100 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-red-700 mb-4">Delete Your Profile</h2>
        <p class="mb-4 text-gray-700">
            Are you sure you want to delete your profile? This action is irreversible. Please confirm by entering your password.
        </p>

        <div class="space-y-4">
            <!-- Password Input -->
            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input
                    type="password"
                    id="password"
                    v-model="password"
                    class="w-full p-2 border border-gray-300 rounded-md"
                />
                <p v-if="errorMessage" class="text-red-500 text-sm mt-2">{{ errorMessage }}</p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-6">
                <button
                    @click="handleCancel"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                    Cancel
                </button>
                <button
                    @click="handleDelete"
                    :disabled="isSubmitting"
                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Deleting...' : 'Delete Profile' }}
                </button>
            </div>
        </div>
    </div>
</template>
