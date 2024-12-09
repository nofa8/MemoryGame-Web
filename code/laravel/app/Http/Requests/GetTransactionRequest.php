<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar conforme as permissões de acesso
    }

    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|integer|exists:users,id',
            'type' => 'sometimes|in:B,P,I',
        ];
    }
}
