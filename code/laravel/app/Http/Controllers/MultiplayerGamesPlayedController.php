<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\StoreMultiGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\MultiGamesResource;
use App\Http\Resources\MultiplayerGamesPlayedResource;
use App\Models\Game;
use App\Models\MultiplayerGamesPlayed;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * Create a new multiplayer game.
     */
    public function create(Request $request)
    {
        $user = $request->user();

        // Validate the request
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'max_players' => 'required|integer|min:2|max:10', // Adjust max as needed
        ]);

        // Check if user has enough brain coins
        if ($user->brain_coins < 5) {
            return response()->json(['error' => 'Insufficient brain coins to create a game.'], 403);
        }

        // Deduct brain coins
        $user->decrement('brain_coins', 5);

        // Create the game
        $game = MultiplayerGamesPlayed::create([
            'board_id' => $request->board_id,
            'created_user_id' => $user->id,
            'max_players' => $request->max_players,
            'status' => 'PE', // Pending
        ]);

        return response()->json(['message' => 'Game created successfully.', 'game' => $game]);
    }

    /**
     * Join an existing multiplayer game.
     */
    public function join(Request $request)
    {
        $user = $request->user();
        // Check if user has enough brain coins
        if ($user->brain_coins < 5) {
            return response()->json(['error' => 'Insufficient brain coins to join a game.'], 403);
        }
        // Validate the request
        $request->validate([
            'game_id' => 'required|exists:multiplayer_games,id',
        ]);

        $game = MultiplayerGamesPlayed::find($request->game_id);

        // Check if game is pending
        if ($game->status !== 'PE') {
            return response()->json(['error' => 'This game is not available to join.'], 403);
        }

        if ($game->players()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'You have already joined this game.'], 403);
        }

        // Check if the game has space
        if ($game->players()->count() >= $game->max_players) {
            return response()->json(['error' => 'This game is full.'], 403);
        }



        // Deduct brain coins and add player to the game
        $user->decrement('brain_coins', 5);
        $game->players()->attach($user->id);

        return response()->json(['message' => 'Joined the game successfully.', 'game' => $game]);
    }

    /**
     * End the game with winner data.
     */
    public function end(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:multiplayer_games,id',
            'winner_id' => 'required|exists:users,id',
        ]);

        $game = MultiplayerGamesPlayed::find($request->game_id);

        // Check if game is in progress
        if ($game->status !== 'PL') {
            return response()->json(['error' => 'This game is not in progress.'], 403);
        }

        // Mark game as ended
        $game->update([
            'status' => 'E',
            'ended_at' => now(),
            'winner_user_id' => $request->winner_id,
        ]);

        // Calculate and distribute rewards
        $totalBrainCoins = $game->players()->count() * 5;
        $platformFee = 3;
        $reward = $totalBrainCoins - $platformFee;

        $winner = User::find($request->winner_id);
        $winner->increment('brain_coins', $reward);

        return response()->json(['message' => 'Game ended successfully.', 'reward' => $reward]);
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

    /**
     * Fetch multiplayer game history for the user.
     */
    public function history()
    {
        $user = Auth::user();

        $games = MultiplayerGamesPlayed::whereHas('players', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('players')->get();

        return response()->json(['games' => $games]);
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
