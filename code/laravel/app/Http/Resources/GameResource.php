<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'time' => $this->total_time,
            'creator' => $this->created_user_id,
            'name' =>  $this?->creator?->nickname,
            // 'winner' => $this->winner_id ,
            'start_time' => $this->began_at,
            'end_time' => $this->ended_at,
            'board' => $this->board_id,
            'turns' => $this->total_turns_winner ?? 100
        ];
    }
}
