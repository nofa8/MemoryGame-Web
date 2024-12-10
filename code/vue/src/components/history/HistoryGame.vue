<script setup>
import { useHistoryStore } from '@/stores/history'

const historyStore = useHistoryStore()

const props = defineProps({
  game: Object
})
</script>

<template>
  <tr class="border-b">
    <td class="px-4 py-3 text-center">{{ historyStore.formatDate(game.start_time) }}</td>
    <td class="px-4 py-3 text-center">{{ game.board_cols }} x {{ game.board_rows }}</td>
    <td class="px-4 py-3 text-center">{{ historyStore.mapStatus(game.status) }}</td>
    <td class="px-4 py-3 text-center">
      {{ game.status == 'I' ? '-' : historyStore.formatTime(game.total_time) }}
    </td>
    <td class="px-4 py-3 text-center">{{ game.status == 'I' ? '-' : game.total_turns }}</td>
    <td class="px-4 py-3 text-center">
      {{ game.players.join(' vs ') == '' ? '-' : game.players.join(' vs ') }}
    </td>
    <td class="px-4 py-3 text-center">{{ game.winner ?? '-' }}</td>
    <td class="px-4 py-3 text-center">{{ game.creator }}</td>
  </tr>
</template>
