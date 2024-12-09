import { defineStore } from 'pinia'
import axios from 'axios'

import { ref } from 'vue'

import { useErrorStore } from '@/stores/error'

export const useBoardsStore = defineStore('boards', () => {
  const boards = ref([])

  const storeError = useErrorStore()

  const loadBoards = async () => {
    storeError.resetMessages()
    try {
      const response = await axios.get('/boards')
      boards.value = response.data.data
    } catch (e) {
      storeError.setErrorMessages(
        e.response.data.message,
        e.response.data.errors,
        e.response.status,
        'Error Getting Boards!'
      )
    }
  }

  return { loadBoards, boards }
})
