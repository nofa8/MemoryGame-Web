<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'date' => $this->transaction_datetime,
            'user'=> $this->user_id,
            'game' => $this->game_id,
            'type' => $this->type,
            'euros' => $this->euros,
            'brain_coins' => $this->brain_coins,
            'payment_type' => $this->payment_type,
            #'payment_reference' => $this->payment_reference,
        ];
    }
}
