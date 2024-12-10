<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { defineProps } from 'vue';

const AuthStore = useAuthStore();

// Props and emits
const props = defineProps({
  user: { type: Object, required: true }
});

const emit = defineEmits();

const password = ref('');
const errorMessage = ref('');
const isSubmitting = ref(false);

const handleDeleteAccount = () => {
    if (!password.value) {
        errorMessage.value = 'Please enter your password to confirm deletion.';
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    // Make sure that the user is authenticated and the ID matches
    

    // Proceed with deleting the user account
    axios
        .delete(`/auth/admin/${props.user.nickname}`, {  // Pass the specific user ID to delete
            data: {
                password: password.value,
            },
        })
        .then(() => {
            console.log('Account deleted successfully.');
             // Log out the authenticated user after deletion
            emit('hidedeleteForm'); // Hide the form after successful deletion
        })
        .catch((error) => {
            if (error.response && error.response.data) {
                errorMessage.value = error.response.data.message || 'Failed to delete the account.';
            } else {
                errorMessage.value = 'An unexpected error occurred.';
            }
        })
        .finally(() => {
            isSubmitting.value = false;
        });

};

const handleCancelDelete = () => {
    emit('hidedeleteForm'); // Emit event to hide the delete confirmation form
};
</script>

<template>
    <div class="max-w-lg mx-auto p-6 bg-red-100 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-red-700 mb-4">Delete Account: {{ user.name }}</h2>
        <p class="mb-4 text-gray-700">
            Are you sure you want to delete the account of {{ user.name }}? This action is irreversible. Please confirm by entering your password.
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
                    @click="handleCancelDelete"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                >
                    Cancel
                </button>
                <button
                    @click="handleDeleteAccount"
                    :disabled="isSubmitting"
                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Deleting...' : 'Delete Account' }}
                </button>
            </div>
        </div>
    </div>
</template>
