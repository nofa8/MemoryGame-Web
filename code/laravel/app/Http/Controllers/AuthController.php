<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;




class AuthController extends Controller
{
    public function createAdmin(Request $request)
    {
        $user = $request->user();
        if ($user->type != 'A') {
            return response()->json([
                'message' => 'Not Admin'
            ], 403);

        }

        try {
            // Validate the incoming request
            $validator = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'nickname' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:3|confirmed',
                'photo' => 'nullable|image|max:2048', // Optional photo
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }


        // Store the user's photo if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
            'photo' => $photoPath, // Save the photo path
            'type' => 'A'
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }



    public function register(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'nickname' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:3|confirmed',
                'photo' => 'nullable|image|max:2048', // Optional photo
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }


        // Store the user's photo if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nickname' => $request->nickname,
            'password' => Hash::make($request->password),
            'photo' => $photoPath, // Save the photo path
            'brain_coins_balance' => 10,
        ]);


        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->brain_coins = 10;
        $transaction->type = 'B';
        $transaction->transaction_datetime = now();


        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }


    private function purgeExpiredTokens()
    {
        // Only deletes if token expired 2 hours ago
        $dateTimetoPurge = now()->subHours(2);
        DB::table('personal_access_tokens')->where('expires_at', '<', $dateTimetoPurge)->delete();
    }

    private function revokeCurrentToken(User $user)
    {
        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', $currentTokenId)->delete();
    }

    public function login(LoginRequest $request)
    {
        $this->purgeExpiredTokens();
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credentials wrong'], 401);
        }
        $token = $request->user()->createToken('authToken', ['*'], now()->addHours(2))->plainTextToken;
        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $this->purgeExpiredTokens();
        $this->revokeCurrentToken($request->user());
        return response()->json(null, 204);
    }

    public function refreshToken(Request $request)
    {
        // Revokes current token and creates a new token
        $this->purgeExpiredTokens();
        $this->revokeCurrentToken($request->user());
        $token = $request->user()->createToken('authToken', ['*'], now()->addHours(2))->plainTextToken;
        return response()->json(['token' => $token]);
    }
}
