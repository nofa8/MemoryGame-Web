<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    { // The rest is done dynamically 
        return [
            'created_user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:S,M',
            'board_id' => 'required|integer|exists:users,id',
        ];
    }
}
