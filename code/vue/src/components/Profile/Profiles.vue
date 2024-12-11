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
const selectedUserType = ref('');
const currentPage = ref(1);

if (!authStore.user) {
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

// Function to fetch users based on filters and page
const filterUsers = (page = 1) => {
    currentPage.value = page; // Update current page
    const query = {
        search: searchQuery.value,
        type: selectedUserType.value,
        page: currentPage.value,
        include_deleted: includeDeleted.value,
    };
    authStore.fetchUsers(query);
};

// Check user authentication and role on mount
onMounted(() => {
    if (authStore.user && authStore.user.type === 'A') {
        filterUsers(); // Fetch users with default parameters
    } else {
        router.push('login');
    }
});

const goToLogin = () => {
    router.push("login");
};

// Toggle inclusion of deleted users
const toggleIncludeDeleted = () => {
    includeDeleted.value = includeDeleted.value ? '' : 'true';
    currentPage.value = 1
    filterUsers(); // Reapply filter after toggling
};

const typeUser = () => {
    currentPage.value = 1
    filterUsers(); // Reapply filter after toggling
};
const searchName = () => {
    currentPage.value = 1
    filterUsers(); // Reapply filter after toggling
};


// Function to get user photo URL
const getPhotoUrl = (photoFileName) => {
    if (photoFileName) {
        return `${axios.defaults.baseURL.replace('/api', '')}/storage/photos/${photoFileName}`;
    }
    return `${axios.defaults.baseURL.replace('/api', '')}/storage/photos/anonymous.jpg`;
};

// Handle user deletion
const handleUserDeleted = (deletedUserId) => {
    if (!includeDeleted.value) {
        authStore.users = authStore.users.filter(user => user.id !== deletedUserId);
    } else {
        const userIndex = authStore.users.findIndex(user => user.id === deletedUserId);
        if (userIndex !== -1) {
            authStore.users[userIndex].deleted_at = new Date().toISOString();
        }
    }
};

// Pagination controls
const goToNextPage = () => {
    if (currentPage.value < authStore.lastPage) {
        filterUsers(currentPage.value + 1);
    }
};

const goToPreviousPage = () => {
    if (currentPage.value > 1) {
        filterUsers(currentPage.value - 1);
    }
};
</script>

<template>
    <div v-if="authStore.user">
        <div v-if="!createAdminform" class="max-w-4xl mx-auto p-6 bg-white rounded-lg">
            <div class="flex flex-wrap justify-between items-center mb-6 space-y-4 md:space-y-0">
                <button
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    @click="createAdmin">
                    Create Administrator
                </button>

                <div class="flex items-center space-x-4">
                    <div @click="toggleIncludeDeleted" :class="includeDeleted ? 'bg-blue-500' : 'bg-gray-300'"
                        class="relative w-11 h-6 rounded-full cursor-pointer transition-colors duration-300">
                        <div :class="includeDeleted ? 'translate-x-5' : 'translate-x-0'"
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300">
                        </div>
                    </div>
                    <span class="text-gray-800 text-sm font-medium">Include Deleted Users</span>

                    <select v-model="selectedUserType" @change="typeUser"
                        class="px-4 py-2 border rounded-md text-gray-700">
                        <option value="">All Users</option>
                        <option value="A">Admins</option>
                        <option value="P">Players</option>
                    </select>

                    <input type="text" v-model="searchQuery" placeholder="Search by name"
                        class="px-4 py-2 border rounded-md text-gray-700" @input="searchName" />
                    <button @click="filterUsers"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Filter
                    </button>
                </div>
            </div>

            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Profiles</h1>

            <div>
                <div v-for="userInfo in authStore.users" :key="userInfo.id" class="mb-6">
                    <ProfileInfo :user="userInfo" :photo-url="getPhotoUrl(userInfo.photo_filename)" @userDeleted="handleUserDeleted" />
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded"
                    :disabled="currentPage === 1" @click="goToPreviousPage">
                    Previous
                </button>

                <span class="self-center">
                    Page {{ currentPage }} of {{ authStore.lastPage }}
                </span>

                <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded"
                    :disabled="currentPage === authStore.lastPage" @click="goToNextPage">
                    Next
                </button>
            </div>
        </div>
        <div v-else>
            <AdminCreate @back-to-profiles="backToProfiles"></AdminCreate>
        </div>
    </div>

    <div v-else class="max-w-3xl mx-auto p-8 bg-gradient-to-r from-purple-500 via-blue-500 to-teal-500 rounded-lg shadow-lg space-y-8">
        <h1 class="text-4xl font-bold text-white text-center md:text-left">You must log in first</h1>
        <p class="text-lg text-white text-center md:text-left">
            To access your profile and other features, please log in to your account.
        </p>
        <div class="flex justify-center md:justify-start mt-6">
            <button @click="goToLogin" class="px-6 py-2 bg-white text-gray-800 rounded-lg shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                Go to Login
            </button>
        </div>
    </div>
</template>
