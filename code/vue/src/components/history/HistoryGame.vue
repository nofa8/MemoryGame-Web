<script setup>
import { useHistoryStore } from '@/stores/history'
const historyStore = useHistoryStore()
const props = defineProps({
  game: Object
})
</script>

<template>
  <tr class="hover:bg-gray-50 transition-colors">
    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ historyStore.formatDate(game.start_time) }}</td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ game.board_cols }} x {{ game.board_rows }}</td>
    <td class="px-4 py-3 text-center">
      <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
        'bg-green-100 text-green-800': game.status === 'E',
        'bg-yellow-100 text-yellow-800': game.status === 'PL',
        'bg-red-100 text-red-800': game.status === 'I',
        'bg-blue-100 text-blue-800': game.status === 'PE'
      }">
        {{ historyStore.mapStatus(game.status) }}
      </span>
    </td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">
      {{ game.status == 'I' ? '-' : historyStore.formatTime(game.total_time) }}
    </td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ game.status == 'I' ? '-' : game.total_turns }}</td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">
      {{ game.players.join(' vs ') == '' ? '-' : game.players.join(' vs ') }}
    </td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ game.winner ?? '-' }}</td>
    <td class="px-4 py-3 text-center text-sm text-gray-500">{{ game.creator }}</td>
  </tr>
</template>