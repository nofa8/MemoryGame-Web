import { ref, computed, inject } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from '@/stores/error'

import avatarNoneAssetURL from '@/assets/avatar-none.png'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  const storeError = useErrorStore()
 
  const user = ref(null)
  const token = ref('')
  const socket = inject('socket')

  const users = ref([])
  const currentPage = ref(1)
  const lastPage = ref(1)
  const total = ref(0)
  const perPage = ref(10)

  const userId = computed(() => {
    return user.value ? user.value.id : -1
  })

  const userName = computed(() => {
    return user.value ? user.value.name : ''
  })

  const userFirstLastName = computed(() => {
    const names = userName.value.trim().split(' ')
    const firstName = names[0] ?? ''
    const lastName = names.length > 1 ? names[names.length - 1] : ''
    return (firstName + ' ' + lastName).trim()
  })

  const userEmail = computed(() => {
    return user.value ? user.value.email : ''
  })

  const userType = computed(() => {
    return user.value ? user.value.type : ''
  })

  const userGender = computed(() => {
    return user.value ? user.value.gender : ''
  })

  const userbrain_coins_balance = computed(() => {
    return user.value ? user.value.brain_coins_balance : ''
  })

  const getFirstLastName = (fullName) => {
    const names = fullName.trim().split(' ')
    const firstName = names[0] ?? ''
    const lastName = names.length > 1 ? names[names.length - 1] : ''
    return (firstName + ' ' + lastName).trim()
  }

  const userPhotoUrl = computed(() => {
    const photoFile = user.value ? (user.value.photoFileName ?? '') : ''
    if (photoFile) {
      return axios.defaults.baseURL.replace('/api', photoFile)
    }
    return avatarNoneAssetURL
  })

  const clearUser = () => {
    resetIntervalToRefreshToken()
    if (user.value) {
      socket.emit('logout', user.value)
    }
    user.value = null
    token.value = ''
    localStorage.removeItem('token')
    axios.defaults.headers.common.Authorization = ''
  }

  const login = async (credentials) => {
    storeError.resetMessages();
    try {
      const responseLogin = await axios.post('auth/login', credentials);
      token.value = responseLogin.data.token;
      localStorage.setItem('token', token.value);
      axios.defaults.headers.common.Authorization = 'Bearer ' + token.value;

      const responseUser = await axios.get('users/me');
      user.value = responseUser.data.data;
      socket.emit('login', user.value);

      repeatRefreshToken();
      
      router.push({ name: 'singlePlayerGames' });

      return user.value;
    } catch (e) {
      clearUser();
      storeError.setErrorMessages(
        e.response.data.message,
        e.response.data.errors,
        e.response.status,
        'Authentication Error!'
      );
      return false;
    }
  }

  const logout = async () => {
    storeError.resetMessages();
    try {
      await axios.post('auth/logout')
      clearUser()
      router.push({name:"singlePlayerGames"})
      return true

    } catch (e) {
      clearUser();
      storeError.setErrorMessages(
        e.response.data.message,
        [],
        e.response.status,
        'Authentication Error!'
      );
      return false;
    }
  }

  let intervalToRefreshToken = null

  const resetIntervalToRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = null
  }
 


  const repeatRefreshToken = () => {
    if (intervalToRefreshToken) {
      clearInterval(intervalToRefreshToken)
    }
    intervalToRefreshToken = setInterval(
      async () => {
        try {
          const response = await axios.post('auth/refreshtoken')
          token.value = response.data.token
          localStorage.setItem('token', token.value)
          axios.defaults.headers.common.Authorization = 'Bearer ' + token.value
          return true
        } catch (e) {
          clearUser()
          storeError.setErrorMessages(
            e.response.data.message,
            e.response.data.errors,
            e.response.status,
            'Authentication Error!'
          )
          return false
        }
      },
      1000 * 60 * 110
    ) // repeat every 110 minutes
    return intervalToRefreshToken
  }

  const restoreLogin = async function () {
    const storedToken = localStorage.getItem('token');
    if (storedToken) {
      try {
        token.value = storedToken;
        axios.defaults.headers.common.Authorization = 'Bearer ' + token.value;
        const responseUser = await axios.get('users/me');
        user.value = responseUser.data.data;
        socket.emit('login', user.value);

        repeatRefreshToken();
        return true;
      } catch {
        clearUser();
        return false;
      }
    }
    return false;
  }

  const setUser = (userData) => {
    user.value = userData
  }
  const fetchUsers = async ({ search = '', type = '', page = 1, include_deleted = false } = {}) => {
    try {
      // Construct the query string including include_deleted
      const response = await axios.get(`/users?page=${page}&search=${search}&type=${type}&include_deleted=${include_deleted}`);

      users.value = response.data.data;
      currentPage.value = response.data.meta.current_page;
      lastPage.value = response.data.meta.last_page;
      total.value = response.data.meta.total;
      perPage.value = response.data.meta.per_page;


    } catch (err) {
      console.error('Failed to load profiles. Please try again later.', err);
    }
  };
  

  return {
    user,
    userId,
    userName,
    userFirstLastName,
    userEmail,
    userType,
    userGender,
    userPhotoUrl,
    userbrain_coins_balance,
    login,
    logout,
    restoreLogin,
    getFirstLastName,
    setUser,
    fetchUsers,
    users,
    currentPage,
    lastPage,
    total,
    perPage
  }
})
