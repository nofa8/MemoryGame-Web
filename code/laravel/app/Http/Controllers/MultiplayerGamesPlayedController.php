<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultiGameUpdateRequest;
use App\Http\Requests\StoreMultiGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\LobbyResource;
use App\Http\Resources\MultiGamesResource;
use App\Http\Resources\MultiplayerGamesPlayedResource;
use App\Models\Game;
use App\Models\MultiplayerGamesPlayed;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MultiplayerGamesPlayedController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index(Request $request)
    // {
    //     if ($request->user()->type == 'A') {
    //         return GameResource::collection(Game::where('type', 'M')->paginate(10));
    //     } else {
    //         $ids = MultiplayerGamesPlayed::where('user_id', $request->user()->id)->pluck('game_id')->toArray();
    //         return GameResource::collection(Game::where('type', 'M')->whereIntegerInRaw('id', $ids)->paginate(10));
    //     }
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMultiGameRequest $request)
    {

        $validated = $request->validated();
        $game = null;

        if (($validated['type'] != 'M')) {
            return response()->json([
                'message' => 'Invalid game type'
            ], 400);
        }



        $user = User::findOrFail($validated['created_user_id']);
        $joined = User::findOrFail($validated['joined_user_id']);
        if ($user->brain_coins_balance < 5) {
            return response()->json([
                'message' => 'User needs to have 5 brain coins to play a Multi player game'
            ], 400);
        }

        if ($joined->brain_coins_balance < 5) {
            return response()->json([
                'message' => 'Joined user needs to have 5 brain coins to play a Multi player game'
            ], 400);
        }

        $game = new Game();
        $game->fill($validated);
        $game->status = 'PL';
        $game->began_at = now();
        $game->ended_at = null;
        $user->brain_coins_balance -= 5;
        $joined->brain_coins_balance -= 5;
        if ($game->save() && $user->save() && $joined->save()) {


            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->game_id = $game->id;
            $transaction->brain_coins = -5;
            $transaction->type = 'I';
            $transaction->transaction_datetime = now();
            $transaction->save();

            $joinedTransaction = new Transaction();
            $joinedTransaction->user_id = $joined->id;
            $joinedTransaction->game_id = $game->id;
            $joinedTransaction->brain_coins = -5;
            $joinedTransaction->type = 'I';
            $joinedTransaction->transaction_datetime = now();
            $joinedTransaction->save();
        }

        $multiplayerGame = new MultiplayerGamesPlayed();

        $multiplayerGame->user_id = $user->id;
        $multiplayerGame->game_id = $game->id;
        $multiplayerGame->save();

        $joinedMultiplayerGame = new MultiplayerGamesPlayed();

        $joinedMultiplayerGame->user_id = $joined->id;
        $joinedMultiplayerGame->game_id = $game->id;
        $joinedMultiplayerGame->save();
        $wow = new MultiGamesResource($game);
        // Log::info('MultiGamesResource content:', $wow->toArray(request()));


        return $wow;
    }

    /**
     * Fetch a list of pending games in the lobby.
     */
    public function lobby()
    {
        $games = MultiplayerGamesPlayed::where('status', 'PE')
            ->with('players') // Load players for each game
            ->get();

        return response()->json(['games' => $games]);
    }

    // /**
    //  * Fetch multiplayer game history for the user.
    //  */
    // public function history()
    // {
    //     $user = Auth::user();

    //     $games = MultiplayerGamesPlayed::whereHas('players', function ($query) use ($user) {
    //         $query->where('user_id', $user->id);
    //     })->with('players')->get();

    //     return response()->json(['games' => $games]);
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(MultiplayerGamesPlayed $multi)
    // {
    //     return new MultiplayerGamesPlayedResource($multi);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(MultiGameUpdateRequest $request, string $id)
    {
        $game = Game::find($id);
        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        } elseif ($game->status === 'I') { // || $game->status === 'E'
            return response()->json(['error' => 'Game is not in progress'], 400);
        }
        if ($game->type === 'S') {
            return response()->json(['error' => 'Not a multiplayer game'], 400);
        }
        $validated = $request->validated();

        $game->status = $validated['status'];
        if ($validated['status'] === 'E') {
            if (
                (!array_key_exists('turns', $validated) ||
                    !array_key_exists('pairs_discovered', $validated)
                    || !array_key_exists('won', $validated) ||
                    !array_key_exists('user_id', $validated))
            ) {
                return response()->json(['error' => 'Incomplete request!'], 400);
            }

            $multi = MultiplayerGamesPlayed::where([['game_id', $game->id], ['user_id', $validated['user_id']]])->first();
            if (!$multi) {
                return response()->json(['error' => 'Player not found in the game'], 404);
            }
            $user = User::findOrFail($validated['user_id']);

            if ($game->created_user_id == $user->id) {
                $game->ended_at = now();
                $this->calculateGameTime($game);
            }
            $multi->player_won = $validated['won'];
            $multi->pairs_discovered = $validated['pairs_discovered'];
            $multi->save();
            if ($validated['won']) {
                $game->total_turns_winner = $validated['turns'];
                $user->brain_coins_balance += 7;

                $transaction = new Transaction([
                    'transaction_datetime' => now(),
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'type' => 'I',
                    'brain_coins' => 7
                ]);
                $user->save();
                $transaction->save();
            }
        }
        $game->save();
        return response()->json(['success' => 'Multiplayer game updated successfully', 'Game' => new GameResource($game)]);
    }


    private function calculateGameTime(Game $game)
    {
        $began_at = Carbon::parse($game->created_at);
        $ended_at = Carbon::parse($game->ended_at);
        $game->total_time = number_format($began_at->floatDiffInSeconds($ended_at), 2);
    }




    // public function end($request)
    // {
    //     $game = MultiplayerGamesPlayed::find($request->game_id);

    //     // Check if game is in progress
    //     if ($game->status !== 'PL') {
    //         return response()->json(['error' => 'This game is not in progress.'], 403);
    //     }

    //     // Mark game as ended
    //     $game->update([
    //         'status' => 'E',
    //         'ended_at' => now(),
    //         'winner_user_id' => $request->winner_id,
    //     ]);

    //     // Calculate and distribute rewards
    //     $totalBrainCoins = $game->players()->count() * 5;
    //     $platformFee = 3;
    //     $reward = $totalBrainCoins - $platformFee;

    //     $winner = User::find($request->winner_id);
    //     $winner->increment('brain_coins', $reward);

    //     return response()->json(['message' => 'Game ended successfully.', 'reward' => $reward]);
    // }


}
