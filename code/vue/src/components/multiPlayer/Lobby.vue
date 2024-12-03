<script setup>
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import { Button } from '@/components/ui/button';
import { onMounted } from 'vue'
import ListGamesLobby from './ListGamesLobby.vue'
import { useLobbyStore } from '@/stores/lobby'

const storeLobby = useLobbyStore()

onMounted(() => {
    storeLobby.fetchGames()
})
</script>

<template>
    <Card class="my-8 py-2 px-1">
        <CardHeader class="pb-0">
            <CardTitle>Lobby</CardTitle>
            <CardDescription>{{ storeLobby.totalGames == 1 ? '1 game' : storeLobby.totalGames + ' games'}} waiting.</CardDescription>
        </CardHeader>
        <CardContent class="p-4">
            <div class="py-2">
                <Button @click="storeLobby.addGame">
                    New Game
                </Button>
            </div>
            <div v-if="storeLobby.totalGames > 0">
                <ListGamesLobby></ListGamesLobby>
            </div>
            <div v-else>
                <h2 class="text-xl">The lobby is empty!</h2>
            </div>
        </CardContent>
    </Card>
</template>