<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'transaction_datetime' => $this->transaction_datetime,
            'user_id' => $this->user_id,
            'nickname' => $this->user != null ? $this->user->nickname : 'Deleted User',
            'game_id' => $this->game_id,
            'euros' => $this->euros,
            'payment_type' => $this->payment_type,
            'payment_reference' => $this->payment_reference,
            'brain_coins' => $this->brain_coins,
            'custom' => $this->custom,
            'user_total_brain_coins' => $this->user != null  ? $this->user->brain_coins_balance : 0
        ];
    }
}
