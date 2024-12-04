import HomeComponent from '@/components/HomeComponent.vue'
import Login from '@/components/Login.vue'
import MultiPlayerGames from '@/components/multiPlayer/MultiPlayerGames.vue'
import SinglePlayerGame from '@/components/singlePlayer/SinglePlayerGame.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes:  [
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
        path: '/multi',
        name: 'multiPlayerGames',
        component: MultiPlayerGames
  },
],
})

export default router
