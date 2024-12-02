import HomeComponent from '@/components/HomeComponent.vue'
import Login from '@/components/Login.vue'
import WebSocketTester from '@/components/WebSocketTester.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeComponent
    },
    {
      path: '/testers',
      children: [
        {
          path: 'websocket',
          component: WebSocketTester
        }
      ]
    },
    {
      path: "/login",
      name: "login",
      component: Login,
  },
  ]
})

export default router
