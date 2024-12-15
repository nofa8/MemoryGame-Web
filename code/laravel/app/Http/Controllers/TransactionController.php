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


    public function storeTAES (Request $request){
        $validated = $request->validate([
            'value' => 'required|integer',
        ]);
        $user = $request->user();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->brain_coins =$validated["value"] ;
        $transaction->type = 'I';
        $transaction->transaction_datetime = now();
        
        $user->brain_coins_balance += $validated["value"];
        $user->save();
        $transaction->save();

        return response()->json(["user"=> $user], 200);


    }

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
        try {
            $validatedData = $request->validated();
            $validatedData['transaction_datetime'] = now();
            if ($validatedData['type'] == 'P') {
                $paymentResult = $this->processPayment(
                    $validatedData['payment_type'],
                    $validatedData['payment_reference'],
                    $validatedData['euros']
                );

                if (!$paymentResult['success']) {
                    return response()->json([
                        'message' => $paymentResult['message']
                    ], $paymentResult['status'] ?? 500);
                } else {
                    $user = $request->user();
                    $user->brain_coins_balance += $validatedData['brain_coins'];
                    $user->save();
                }
            }

            $transaction = Transaction::create($validatedData);
            return new TransactionResource($transaction);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unknown error'
            ], 500);
        }
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
                    'message' => $response->json('message', 'Payment processed successfully'),
                    'status' => $response->status(),
                ];
            } else {
                // Processar erros específicos
                return [
                    'success' => false,
                    'message' => $response->json('message', 'Unknown error'),
                    'status' => $response->status(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error connecting to the payments service.',
                'status' => 500
            ];
        }
    }
}
