<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\BoardResource;
use App\Models\MultiplayerGamesPlayed;

class MultiGamesResource extends JsonResource
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
            'player1' => $this?->creator ?? 'Unknown User',
            'status' => $this->status,
            'board' => new BoardResource($this->board),
            'player2' => $this->multiplayerGamesPlayed
                ->where('user_id', '!=', $this->created_user_id) 
                ->first()?->user ?? 'Unknown User'
        ];
    }
}
