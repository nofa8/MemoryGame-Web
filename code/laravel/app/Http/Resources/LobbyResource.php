<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BoardResource;
use App\Models\MultiplayerGamesPlayed;

class LobbyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created' => $this?->creator?->nickname ?? 'Unknown User',
            'winner' => $this?->winner?->nickname ?? 'Unknown User',
            'type' => $this->type,
            'status' => $this->status,
            'began_at' => $this->began_at,
            'ended_at' => $this->ended_at,
            'total_time' => $this->total_time,
            'board_id' => new BoardResource($this->board),
            'total_turns_winner' => $this->total_turns_winner,
            'participants' => $this->multiplayerGamesPlayed->map(function ($multiplayer) {
                if ($multiplayer->user) {
                    return [
                        'user_nickname' => $multiplayer->user->nickname,
                        'pairs_discovered' => $multiplayer->pairs_discovered,
                    ];
                }
                return null;
            })->filter()
        ];
    }
}
