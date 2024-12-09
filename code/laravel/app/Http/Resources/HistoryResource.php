<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status, 
            'total_time' => $this->total_time, 
            'creator' => $this->creator->nickname,
            'winner' => $this?->winner?->nickname,
            'start_time' => $this->began_at,
            'end_time' => $this->ended_at,
            'board_cols' => $this->board->board_cols,
            'board_rows' => $this->board->board_rows,
            'total_turns' => $this->total_turns_winner ?? 0,
            'players' => $this->multiplayerGamesPlayed->pluck('user.nickname')
        ];
    }
}