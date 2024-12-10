<script setup>
import { ref, onMounted } from 'vue';

import { useAuthStore } from '@/stores/auth';
import ProfileInfo from './ProfileInfo.vue';
import AdminCreate from './AdminCreate.vue';
import router from '@/router';
import axios from 'axios';

const authStore = useAuthStore();
const includeDeleted = ref('');
const createAdminform = ref(false);
const searchQuery = ref('');
const selectedUserType = ref(''); // '' for all, 'A' for Admin, 'P' for Player

if (!authStore.user) {
    // Redirect to login if the user is not authenticated
    router.push("login");
}
// Function to toggle the admin creation form
const createAdmin = () => {
    createAdminform.value = true;
};

// Function to return to the profile list view
const backToProfiles = () => {
    createAdminform.value = false;
};

// Function to filter users by search query and user type
const filterUsers = () => {
    const query = {
        search: searchQuery.value,
        type: selectedUserType.value, // Include type in the filter
        include_deleted: includeDeleted.value  // Include or exclude deleted users
    };
    authStore.fetchUsers(query); // Pass the updated query to the store
};

// Check user authentication and role on mount
onMounted(() => {
    if (authStore.user && authStore.user.type == 'A') {
        authStore.fetchUsers(); // Fetch all users by default
    } else {
        router.push('login');
    }
});

const goToLogin = () => {
    router.push("login");
};
const toggleIncludeDeleted = () => {
    includeDeleted.value = includeDeleted.value ? '' : 'true'; // Toggle between '' and 'true'
};
// Function to get user photo URL
const getPhotoUrl = (photoFileName) => {
    if (photoFileName) {
        return `${axios.defaults.baseURL.replace('/api', '')}/storage/photos/${photoFileName}`;
    }
    return `${axios.defaults.baseURL.replace('/api', '')}/storage/photos/anonymous.jpg`;
};
</script>


<template>
    <div v-if="authStore.user">
        <div v-if="!createAdminform" class="max-w-4xl mx-auto p-6 bg-white rounded-lg">
            <!-- Header with Create Admin button, User Type Filter, and Search filter -->
            <div class="flex flex-wrap justify-between items-center mb-6 space-y-4 md:space-y-0">
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    @click="createAdmin">
                    Create Administrator
                </button>

                <!-- Search and Filters -->
                <div class="flex items-center space-x-4">
                    <!-- Toggle Switch -->
                    <div @click="toggleIncludeDeleted" :class="includeDeleted ? 'bg-blue-500' : 'bg-gray-300'"
                        class="relative w-11 h-6 rounded-full cursor-pointer transition-colors duration-300">
                        <div :class="includeDeleted ? 'translate-x-5' : 'translate-x-0'"
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300">
                        </div>
                    </div>
                    <span class="text-gray-800 text-sm font-medium">Include Deleted Users</span>

                    <!-- User Type Dropdown -->
                    <select v-model="selectedUserType" @change="filterUsers"
                        class="px-4 py-2 border rounded-md text-gray-700">
                        <option value="">All Users</option>
                        <option value="A">Admins</option>
                        <option value="P">Players</option>
                    </select>

                    <!-- Search Filter -->
                    <input type="text" v-model="searchQuery" placeholder="Search by name"
                        class="px-4 py-2 border rounded-md text-gray-700" @input="filterUsers" />
                    <button @click="filterUsers"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Filter
                    </button>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Profiles</h1>

            <div>
                <!-- Iterate over users and pass the photo URL to ProfileInfo -->
                <div v-for="userInfo in authStore.users" :key="userInfo.id" class="mb-6">
                    <ProfileInfo :user="userInfo" :photo-url="getPhotoUrl(userInfo.photo_filename)" />
                </div>
            </div>
        </div>
        <div v-if="createAdminform">
            <AdminCreate @back-to-profiles="backToProfiles"></AdminCreate>
        </div>
    </div>

    <div v-else
        class="max-w-3xl mx-auto p-8 bg-gradient-to-r from-purple-500 via-blue-500 to-teal-500 rounded-lg shadow-lg space-y-8">
        <h1 class="text-4xl font-bold text-white text-center md:text-left">
            You must log in first
        </h1>
        <p class="text-lg text-white text-center md:text-left">
            To access your profile and other features, please log in to your account.
        </p>

        <!-- Optionally, you can add a login button here -->
        <div class="flex justify-center md:justify-start mt-6">
            <button @click="goToLogin"
                class="px-6 py-2 bg-white text-gray-800 rounded-lg shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                Go to Login
            </button>
        </div>
    </div>

</template>
