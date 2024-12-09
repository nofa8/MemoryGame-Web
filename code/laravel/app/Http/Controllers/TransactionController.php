<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\GetTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;


class TransactionController extends Controller
{


    public function index(Request $request)
    {
        $user = $request->user();
        $query = Transaction::query();

        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($user->type !== 'A') {
            $query->where('user_id', $user->id);
        }

        $transactions = $query->orderBy('transaction_datetime', 'desc')->paginate(10);

        return response()->json([
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }


    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        return new TransactionResource($transaction);
    }

    public function store(StoreTransactionRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['transaction_datetime'] = now();


        $paymentResult = $this->processPayment(
            $validatedData['payment_type'],
            $validatedData['payment_reference'],
            $validatedData['euros']
        );

        if (!$paymentResult['success']) {
            return response()->json([
                'message' => $paymentResult['message'],
                'error' => $paymentResult['error'] ?? null,
            ], $paymentResult['status'] ?? 500);
        }

        $transaction = Transaction::create($validatedData);
        return new TransactionResource($transaction);
    }

    public function processPayment($type, $reference, $value)
    {
        $url = 'https://dad-202425-payments-api.vercel.app/api/debit';

        try {
            // Enviar requisição POST ao serviço
            $response = Http::post($url, [
                'type' => $type,
                'reference' => $reference,
                'value' => $value,
            ]);

            if ($response->successful()) {
                // Processar sucesso
                return [
                    'success' => true,
                    'message' => 'Pagamento processado com sucesso.',
                    'data' => $response->json(),
                ];
            } else {
                // Processar erros específicos
                return [
                    'success' => false,
                    'message' => $response->json('message', 'Erro desconhecido.'),
                    'status' => $response->status(),
                ];
            }
        } catch (\Exception $e) {
            // Tratar erros de conexão ou outros problemas
            return [
                'success' => false,
                'message' => 'Erro ao conectar ao serviço de pagamentos.',
                'error' => $e->getMessage(),
            ];
        }
    }

}
