<script setup>

const props = defineProps({
    game: Object
});
</script>

<template>
    <tr class="border-b">
        <td class="px-4 py-3 text-center">{{ game.start_time }}</td>
        <td class="px-4 py-3 text-center">{{ game.board_rows }} x {{ game.board_cols }}</td>
        <td class="px-4 py-3 text-center">{{ game.status }}</td>
        <td class="px-4 py-3 text-center">{{ game.total_time }}</td>
        <td class="px-4 py-3 text-center">{{ game.total_turns }}</td>
        <td class="px-4 py-3 text-center">{{ game.players.join(' vs ') }}</td>
        <td class="px-4 py-3 text-center">{{ game.winner }}</td>
        <td class="px-4 py-3 text-center">{{ game.creator }}</td>
    </tr>
</template>