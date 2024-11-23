<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MultiplayerGamesPlayedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id,
            'user' => $this->user_id, # new UserResource($this->user)
            'game' => $this->game_id,# new GameResource($this->game)
            'win' => $this->player_won, # 1 -> won, 0 -> lost
            'pairs_discovered' => $this->pairs_discovered
        ];
    }
}
