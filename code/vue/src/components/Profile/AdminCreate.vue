<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['backToProfiles']);

// Form state
const name = ref('');
const email = ref('');
const nickname = ref('');
const password = ref('');
const passwordConfirm = ref('');
const photo = ref(null);
const photoPreview = ref(null); // For photo preview

// Error handling
const formErrors = ref({
    name: '',
    email: '',
    nickname: '',
    password: '',
    passwordConfirm: '',
    photo: ''
});

// Function to handle photo upload
const handlePhotoUpload = (e) => {
    const file = e.target.files[0];
    photo.value = file;
    if (file) {
        photoPreview.value = URL.createObjectURL(file); // Create preview URL
    }
};

// Function to validate form
const validateForm = () => {
    formErrors.value = { name: '', email: '', nickname: '', password: '', passwordConfirm: '', photo: '' };
    let valid = true;

    if (!name.value) {
        formErrors.value.name = 'Name is required';
        valid = false;
    }
    if (!email.value || !/\S+@\S+\.\S+/.test(email.value)) {
        formErrors.value.email = 'A valid email is required';
        valid = false;
    }
    if (!nickname.value) {
        formErrors.value.nickname = 'Nickname is required';
        valid = false;
    }
    if (!password.value || password.value.length < 3) {
        formErrors.value.password = 'Password must be at least 3 characters long';
        valid = false;
    }
    if (password.value !== passwordConfirm.value) {
        formErrors.value.passwordConfirm = 'Passwords do not match';
        valid = false;
    }

    return valid;
};

// Function to create a new administrator
const createAdministrator = () => {
    if (!validateForm()) {
        return; // Exit if form is invalid
    }

    // Create form data for file upload
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('email', email.value);
    formData.append('nickname', nickname.value);
    formData.append('password', password.value);
    formData.append('password_confirmation', passwordConfirm.value);
    if (photo.value) {
        formData.append('photo', photo.value);
    }

    // Make the API request
    axios.post('/auth/admin', formData)
        .then((response) => {
            alert('Administrator created successfully!');
            // Optionally reset the form after successful submission
            name.value = '';
            email.value = '';
            nickname.value = '';
            password.value = '';
            passwordConfirm.value = '';
            photo.value = null;
            photoPreview.value = null;
        })
        .catch((error) => {
            console.error('Error creating administrator:', error);
            if (error.response && error.response.data.errors) {
                for (const [key, value] of Object.entries(error.response.data.errors)) {
                    if (formErrors.value[key] !== undefined) {
                        formErrors.value[key] = value[0];
                    }
                }
            }
        })
        
        

};

const backToProfiles = () => {
    emit('backToProfiles'); // Emit event to hide the delete form
};
</script>
<template>
    <div class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Create New Administrator</h1>

        <div class="space-y-4">
            <!-- Name -->
            <div>
                <label for="name" class="block text-gray-700">Name</label>
                <input type="text" id="name" v-model="name" class="w-full p-2 border border-gray-300 rounded-md" />
                <p v-if="formErrors.name" class="text-red-500 text-sm">{{ formErrors.name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" v-model="email" class="w-full p-2 border border-gray-300 rounded-md" />
                <p v-if="formErrors.email" class="text-red-500 text-sm">{{ formErrors.email }}</p>
            </div>

            <!-- Nickname -->
            <div>
                <label for="nickname" class="block text-gray-700">Nickname</label>
                <input type="text" id="nickname" v-model="nickname"
                    class="w-full p-2 border border-gray-300 rounded-md" />
                <p v-if="formErrors.nickname" class="text-red-500 text-sm">{{ formErrors.nickname }}</p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" v-model="password"
                    class="w-full p-2 border border-gray-300 rounded-md" />
                <p v-if="formErrors.password" class="text-red-500 text-sm">{{ formErrors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="passwordConfirm" class="block text-gray-700">Confirm Password</label>
                <input type="password" id="passwordConfirm" v-model="passwordConfirm"
                    class="w-full p-2 border border-gray-300 rounded-md" />
                <p v-if="formErrors.passwordConfirm" class="text-red-500 text-sm">{{ formErrors.passwordConfirm }}</p>
            </div>

            <!-- Photo Upload -->
            <div>
                <label for="photo" class="block text-gray-700">Profile Photo</label>
                <input type="file" id="photo" @change="handlePhotoUpload"
                    class="w-full p-2 border border-gray-300 rounded-md" />
                <div v-if="photoPreview" class="mt-2">
                    <img :src="photoPreview" alt="Profile Preview" class="w-24 h-24 rounded-full object-cover border" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button @click="createAdministrator"
                    class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Create
                    Administrator</button>
            </div>
            <div class="mt-4">
                <button @click="backToProfiles"
                    class="w-full py-2 bg-gray-200 text-black rounded-md hover:bg-gray-500">Back</button>
            </div>
        </div>
    </div>
</template>