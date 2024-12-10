
import HistoryTable from '@/components/history/HistoryTable.vue'
import Login from '@/components/Login.vue'
import MultiPlayerGames from '@/components/multiPlayer/MultiPlayerGames.vue'
import Profile from '@/components/Profile/Profile.vue'
import Profiles from '@/components/Profile/Profiles.vue'
import Register from '@/components/Profile/Register.vue'
import Board from '@/components/singlePlayer/Board.vue'
import SinglePlayerGame from '@/components/singlePlayer/SinglePlayerGame.vue'
import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'singlePlayerGames',
      component: SinglePlayerGame
    },
    {
      path: '/single',
      redirect: { name: 'singlePlayerGames' }
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/game',
      name: 'game',
      component: Board
    },
    {
      path: '/multi',
      name: 'multiPlayerGames',
      component: MultiPlayerGames
    },
    {
      path: '/history',
      name: 'history',
      component: HistoryTable
    },
    {
      path: '/profile',
      name: 'profile',
      component: Profile
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/profiles',
      name: 'profiles',
      component: Profiles
    },
  ],
})

let firstTime = true

router.beforeEach(async (to, from, next) => {
  const storeAuth = useAuthStore()


  if (firstTime) {
    firstTime = false
    if (localStorage.getItem('token') != null) {
      await storeAuth.restoreLogin()
    }

    if (to.name == 'game') {
      router.push({ name: "singlePlayerGames" });
    }

  }

  if (((to.name == 'profiles') || (to.name == 'profile')) && (!storeAuth.user)) {
    next({ name: 'login' })
    return
  }

  next()
})


export default router
