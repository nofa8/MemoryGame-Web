<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;



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


}
