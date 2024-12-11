import HistoryTable from '@/components/history/HistoryTable.vue'
import Login from '@/components/Login.vue'
import MultiPlayerGames from '@/components/multiPlayer/MultiPlayerGames.vue'

import Profile from '@/components/Profile/Profile.vue'
import Profiles from '@/components/Profile/Profiles.vue'
import Register from '@/components/Profile/Register.vue'


import ScoreGlobal from '@/components/scoreBoards/ScoreGlobal.vue'
import ScorePersonal from '@/components/scoreBoards/ScorePersonal.vue'


import Board from '@/components/singlePlayer/Board.vue'

import SinglePlayerGame from '@/components/singlePlayer/SinglePlayerGame.vue'
import TransactionsTable from '@/components/transactions/TransactionsTable.vue'
import Purchases from '@/components/transactions/PurchasesPage.vue'

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
      component: MultiPlayerGames},

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
  
   
    {
      path: '/history',
      name: 'history',
      component: HistoryTable
    },
    {
      path: '/scoreboardpersonal',
      name: 'scoreboardPersonal',
      component: ScorePersonal
    },
    {
      path: '/scoreboardglobal',
      name: 'scoreboardGlobal',
      component: ScoreGlobal
    },
    {
      path: '/transactions',
      name: 'transactions',
      component: TransactionsTable
    },
    {
      path: '/purchase',
      name: 'Purchase',
      component: Purchases
    }
  ]
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
      router.push({ name: 'singlePlayerGames' })
    }
  }


  if (storeAuth.user == null) {
    if (to.name == 'scoreboardPersonal') {
      router.push({ name: 'scoreboardGlobal' })
    } else if (to.name == 'history') {
      router.push({ name: 'singlePlayerGames' })
    } else if (to.name == 'transactions') {
      router.push({ name: 'singlePlayerGames' })
    }
    else if (((to.name == 'profiles') || (to.name == 'profile')) && (!storeAuth.user)) {
    router.push({ name: 'login' })
    }
  } else {
    if (to.name == 'login') {
      router.push({ name: 'Purchase' })
    }

    if (storeAuth.user.type == 'A') {
      if (to.name == 'Purchase') {
        router.push({ name: 'transactions' })
      }
      if (to.name == 'scoreboardPersonal') {
        router.push({ name: 'scoreboardGlobal' })
      }

      if (to.name == 'singlePlayerGames' || to.name == 'multiPlayerGames' || to.name == 'game') {
        router.push({ name: 'history' })
      }
    }
  }


 


  next()
})


export default router
