<script setup>
import { useAuthStore } from '@/stores/auth';
import { ref } from 'vue';
import ProfileForm from './ProfileForm.vue';
import ProfileDelete from './ProfileDelete.vue';
import router from '@/router';
import UpdatePicture from './UpdatePicture.vue';

const authStore = useAuthStore();
const showChangePicture = ref(false);
const showProfileForm = ref(false);
const formDelete = ref(false);
const toggleProfileForm = () => {
    showProfileForm.value = true;
};
const showDeleteForm = () => {
    formDelete.value = true;
};
const hidedeleteForm = () => {
    formDelete.value = false;
};
const backToProfile = () => {
    showProfileForm.value = false;
};

const updateUserPhoto = () => {
    showChangePicture.value = false; // Optionally close modal
};

const toggleChangePicture = () => {
    showChangePicture.value = !showChangePicture.value;
};

const goToLogin = () => {
    // Redirect to login page, you can modify this as per your routing setup
    router.push({ name: 'login' });
};
</script>

<template>
    <div v-if="authStore.user" class="max-w-3xl mx-auto p-8 bg-white rounded-lg shadow-lg space-y-8">
        <!-- Welcome Header -->
        <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center md:text-left">
            Welcome, {{ authStore.userFirstLastName }}!
        </h1>

        <!-- Profile Information Section -->
        <div class="space-y-6 md:space-y-0 md:flex md:justify-between items-center md:items-start bg-white shadow-lg rounded-lg p-6">
            <!-- Left Side: Profile Picture and User Info -->
            <div class="flex items-center space-x-6">
                <!-- Profile Picture -->
                <div class="relative flex-shrink-0 group">
                    <!-- Profile Picture -->
                    <img :src="authStore.userPhotoUrl" alt="Profile Picture"
                        class="w-24 h-24 rounded-full object-cover border-2 border-gray-300 shadow-sm cursor-pointer"
                        @click="toggleChangePicture" />

                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        @click="toggleChangePicture">
                        <span class="text-white text-sm font-medium" @click="toggleChangePicture">Edit</span>
                    </div>
                </div>

                <!-- User Information -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ authStore.user.name }}</h2>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium">Nickname:</span> {{ authStore.user.nickname }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Email:</span> {{ authStore.user.email }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Type:</span> {{ authStore.user.type == 'P' ? 'Player' : 'Admin' }}
                    </p>
                    <p v-if="authStore.user.type == 'P'" class="text-sm text-gray-600">
                        <span class="font-medium">Brain Coins:</span> {{ authStore.user.brain_coins_balance }}
                    </p>
                </div>
            </div>
        </div>
        <div v-if="showChangePicture">
            <UpdatePicture @hideChangePicture="toggleChangePicture" @picture-updated="updateUserPhoto" />
        </div>

        <!-- Buttons for Updating and Removing Account -->
        <div class="flex justify-center md:justify-start gap-4 mt-6">
            <!-- Update Profile Button -->
            <button v-if="!showProfileForm && !formDelete" @click="toggleProfileForm"
                class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                Update Profile
            </button>
            <!-- Show Profile Form if `showProfileForm` is true -->
            

            <!-- Remove Account Button -->
            <button v-if="!formDelete && !showProfileForm && authStore.userType == 'P'" @click="showDeleteForm"
                class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                Remove Account
            </button>

            <div v-if="formDelete" class="mt-6">
                <ProfileDelete @hidedeleteForm="hidedeleteForm" /> <!-- Profile Form Component -->
            </div>
            <button v-if="!formDelete && !showProfileForm && authStore.userType == 'A'" @click="router.push('profiles')"
                class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                All Profiles
            </button>
        </div>
        <div v-if="showProfileForm" class="mt-6">
                <ProfileForm @back-to-profile="backToProfile" /> <!-- Profile Form Component -->
            </div>
    </div>

    <!-- If the user is not authenticated, show login message -->
    <div v-else class="max-w-3xl mx-auto p-8 bg-gradient-to-r from-purple-500 via-blue-500 to-teal-500 rounded-lg shadow-lg space-y-8">
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
