<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    //– Type of transaction ('B' for bonus, 'P' for purchases, 'I' for internal spending/earnings
    //related to a game


    // – Game ID linked to the transaction (only applicable for type 'I')

    // euros – Value of the purchase transaction (in euros; only applicable for type 'P').
    protected $fillable = [
        'transaction_datetime',
        'user_id',
        'game_id',
        'type',
        'euros',
        'brain_coins',
        'payment_type',
        'payment_reference'
    ];

    public function user() : BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function game() : BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}