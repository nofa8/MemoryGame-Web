import Login from '@/components/Login.vue'
import MultiPlayerGames from '@/components/multiPlayer/MultiPlayerGames.vue'
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
    }
  ]
})

let firstTime = true

router.beforeEach(async (to, from, next) => {
  const storeAuth = useAuthStore()

  if (firstTime ) {
    firstTime = false
    if (localStorage.getItem('token') != null){
      await storeAuth.restoreLogin()
      
    }

    if (to.name == 'game' ){
      router.push({ name: "singlePlayerGames" });
    }

  }
  
  next()
})
export default router
