<script setup>
import ScorePersonalSingle from '@/components/scoreBoards/ScorePersonalSingle.vue';
import ScorePersonalMulti from '@/components/scoreBoards/ScorePersonalMulti.vue';
</script>

<template>
    <div class="container mx-auto p-4 space-y-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Personal Scoreboards</h2>

        <ScorePersonalSingle />
        <ScorePersonalMulti />
    </div>
</template>