<script setup>
import { ref } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
</script>

<template>
  <Card
    class="w-1/3 mx-auto my-8 p-4 px-8 bg-white dark:bg-gray-800 border-0 flex justify-center items-center"
  >
    <CardHeader>
      <CardTitle class="text-black dark:text-white">Memory Card Game</CardTitle>
      <CardDescription>The best memory card game ever!</CardDescription>
    </CardHeader>
    <CardContent>
      <RouterLink
        :to="{ name: 'game' }"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        Start Game
      </RouterLink>
    </CardContent>
  </Card>
</template>
