<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public $timestamps = false;


    protected $fillable = [
        'type',
        'transaction_datetime',
        'user_id',
        'game_id',
        'euros',
        'payment_type',
        'payment_reference',
        'brain_coins',
        'custom',
    ];

    protected $casts = [
        'custom' => 'array',
    ];



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

}
