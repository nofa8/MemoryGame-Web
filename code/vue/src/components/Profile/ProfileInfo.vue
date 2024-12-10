<script setup>
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import { computed, ref } from 'vue';
import DeleteUserAsAdmin from './DeleteUserAsAdmin.vue';

const authStore = useAuthStore();

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    photoUrl: {
        type: String,
        required: true,
    },
});

const formDelete = ref(false);

const blockAccount = async () => {
    try {
        // Send a request to the backend to toggle the block/unblock status
        const response = await axios.patch(`/users/${props.user.nickname}`);

        // If the response is successful, update the blocked status locally
        if (response.status === 200) {
            props.user.blocked = response.data.user.blocked;  // Update the blocked status
            const message = props.user.blocked ? 'Account has been blocked.' : 'Account has been unblocked.';
            alert(message);
        }
    } catch (err) {
        console.error('Failed to toggle the account status:', err);
        alert('There was an error toggling the account status. Please try again later.');
    }
};

const restoreAccount = async () => {
    try {
        // Send a request to the backend to restore the account
        const response = await axios.patch(`/auth/admin/${props.user.nickname}`);

        if (response.status === 200) {
            props.user.deleted_at = null;  // Set deleted_at to null (restore the user)
            alert('Account has been restored.');
        }
    } catch (err) {
        console.error('Failed to restore the account:', err);
        alert('There was an error restoring the account. Please try again later.');
    }
};

const showDeleteForm = () => {
    formDelete.value = true;
};

const hidedeleteForm = () => {
    formDelete.value = false;
};

const fullName = computed(() => props.user.name);
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow-lg max-w-4xl mx-auto">
        <div class="flex items-center justify-between space-x-6">
            <!-- Profile Picture and User Info -->
            <div class="flex items-center space-x-6 flex-grow">
                <img :src="props.photoUrl" alt="Profile Picture"
                    class="w-20 h-20 rounded-full object-cover border-2 border-gray-300" />
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ fullName }}</h2>
                    <p class="text-sm text-gray-600">Nickname: {{ user.nickname }}</p>
                    <p class="text-sm text-gray-600">Email: {{ user.email }}</p>
                    <p class="text-sm text-gray-600">Type: {{ user.type == 'P' ? 'Player' : 'Admin' }}</p>
                    <p v-if="user.type == 'P'" class="text-sm text-gray-600">Brain Coins: {{ user.brain_coins_balance }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div v-if="user.nickname != authStore.user.nickname" class="flex flex-col space-y-2">
                <!-- Remove Account Button -->
                <div>
                    <!-- Button to trigger delete form visibility -->
                    <button v-if="!user.deleted_at " @click="showDeleteForm" class="px-4 py-2 bg-red-500 text-white rounded-md">
                        Remove Account
                    </button>

                    <!-- Conditionally render the delete form -->
                    
                </div>

                <!-- Block Account Button -->
                <button v-if="!user.blocked && !user.deleted_at" @click="blockAccount"
                    class="px-6 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition duration-200">
                    Block Account
                </button>
                <button v-else-if="!user.deleted_at" @click="blockAccount"
                    class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition duration-200">
                    Unblock Account
                </button>

                <!-- Restore Account Button (appears if the user is soft-deleted) -->
                <button v-if="user.deleted_at" @click="restoreAccount"
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200">
                    Restore Account
                </button>
            </div>

        </div>
        <div v-if="formDelete" class="mt-6">
            <DeleteUserAsAdmin :user="user" @hidedeleteForm="hidedeleteForm" />
        </div>
    </div>
</template>
