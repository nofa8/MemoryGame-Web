<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\MultiGamesResource;
use App\Http\Resources\MultiplayerGamesPlayedResource;
use App\Models\Game;
use App\Models\MultiplayerGamesPlayed;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class MultiplayerGamesPlayedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->type == 'A') {
            return GameResource::collection(Game::where('type', 'M')->paginate(10));
        } else {
            $ids = MultiplayerGamesPlayed::where('user_id', $request->user()->id)->pluck('game_id')->toArray();
            return GameResource::collection(Game::where('type', 'M')->whereIntegerInRaw('id', $ids)->paginate(10));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {

        $validated = $request->validated();
        $game = null;

        if (($validated['type'] != 'M')) {
            return response()->json([
                'message' => 'Invalid game type'
            ], 400);
        }
        $user = User::findOrFail($validated['created_user_id']);
        if ($user->brain_coins_balance < 5) {
            return response()->json([
                'message' => 'User needs to have 5 brain coins to play a Multi player game'
            ], 400);
        }

        $game = new Game();
        $game->fill($validated);
        $game->status = 'PE';
        $game->began_at = now();
        $game->ended_at = null;
        $user->brain_coins_balance -= 5;

        if ($game->save() && $user->save()) {

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->game_id = $game->id;
            $transaction->brain_coins = -5;
            $transaction->type = 'I';
            $transaction->transaction_datetime = now();
            $transaction->save();
        }

        $multiplayerGame = new MultiplayerGamesPlayed();

        $multiplayerGame->user_id = $user->id;
        $multiplayerGame->game_id = $game->id;
        $multiplayerGame->save();

        return new MultiGamesResource($game);
    }


    /**
     * Display the specified resource.
     */
    public function show(MultiplayerGamesPlayed $multi)
    {
        return new MultiplayerGamesPlayedResource($multi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
