<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< Updated upstream
=======
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> Stashed changes
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    //type – User type ('A' for administrator; 'P' for player), 
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'nickname',
        'photo_filename',
        'blocked',
        'brain_coins_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked' => 'boolean',
        ];
    }
    public function createdGames(): HasMany
    {
        return $this->hasMany(Game::class, 'created_user_id');
    }
    public function gamesWon(): HasMany
    {
        return $this->hasMany(Game::class, 'winner_user_id');
    }
    public function multiplayerGamesPlayed(): HasMany
    {
        return $this->hasMany(MultiplayerGamesPlayed::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
