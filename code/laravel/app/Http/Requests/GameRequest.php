<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as needed
    }

    public function rules()
    {
        return [
            'type' => 'required|string|max:255',
            'status' => 'required|in:PE,PL,E,I',
            'total_time' => 'nullable|integer',
            'created_user_id' => 'required|exists:users,id',
            'winner_user_id' => 'nullable|exists:users,id',
            'began_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:began_at',
            'board_id' => 'required|exists:boards,id',
            'total_turns_winner' => 'nullable|integer',
        ];
    }
}
