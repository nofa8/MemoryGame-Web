<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar conforme as permissões de acesso
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:B,P,I',
            'user_id' => 'required|integer|exists:users,id',
            'game_id' => 'nullable|integer|exists:games,id',
            'euros' => 'nullable|numeric',
            'payment_type' => 'nullable|in:MBWAY,IBAN,MB,VISA,PAYPAL',
            'payment_reference' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $paymentType = $this->input('payment_type');
                    $validators = [
                        'MBWAY' => '/^9\d{8}$/',
                        'IBAN' => '/^[A-Z]{2}\d{23}$/',
                        'MB' => '/^\d{5}-\d{9}$/',
                        'VISA' => '/^4\d{15}$/',
                        'PAYPAL' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
                    ];

                    if (isset($validators[$paymentType]) && !preg_match($validators[$paymentType], $value)) {
                        $fail("The {$attribute} format is invalid for the selected payment type ({$paymentType}).");
                    }
                },
            ],
            'brain_coins' => 'required|integer',
            'custom' => 'nullable|json',
        ];
    }

}
