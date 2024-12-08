<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MultiGameUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'turns' => 'sometimes|integer',
            'status' => 'required|string|in:E,I',
            'pairs_discovered' => 'sometimes|integer',
            'won' => 'sometimes|integer|in:0,1',
            'user_id' => 'sometimes|integer|exists:users,id',
        ];
    }
}
