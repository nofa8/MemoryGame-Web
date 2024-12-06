<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Requests\GameUpdateRequest;
use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\MultiplayerGamesPlayed;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::where('status', 'E')->with(['creator'])->take(10)->get();
        return GameResource::collection($games);
    }
    //returns singleplayer games
    public function indexSingle(Request $request)
    {
        if ($request->user()->type == 'A') {
            return GameResource::collection(Game::where('type', 'S')->paginate(10));
        } else {
            return GameResource::collection(Game::where('type', 'S')->where('created_user_id', $request->user()->id)->paginate(10));
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function storeTAES(GameRequest $request)
    {
        $game = Game::create($request->validated());

        return response()->json([
            'message' => 'Game created successfully!'
        ], 201);
    }

    public function store(StoreGameRequest $request)
    {

        $validated = $request->validated();
        $game = null;

        // Wrong Place to store
        if (($validated['type'] != 'S')) {
            return response()->json([
                'message' => 'Game type not acceptable '
            ], 400);
        }


        if ($validated['board_id'] == 1) {
            $game = new Game();
            $game->fill($validated);
            $game->status = 'PL'; //Single player starts in progress
            $game->began_at = now();
            $game->ended_at = null;
            $game->save();
        } else {
            $user = User::findOrFail($validated['created_user_id']);
            if ($user->brain_coins_balance < 1) {
                return response()->json([
                    'message' => 'User must have at least 1 brain coin to play this type of board'
                ], 400);
            }

            $game = new Game();
            $game->fill($validated);
            $game->status = 'PL';
            $game->began_at = now();
            $game->ended_at = null;
            $user->brain_coins_balance--;

            if ($game->save() && $user->save()) {

                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->game_id = $game->id;
                $transaction->brain_coins = -1;
                $transaction->type = 'I';
                $transaction->transaction_datetime = now();
                $transaction->save();
            }
        }

        return new GameResource($game);
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Game $game)
    // {
    //     return new GameResource($game);
    // }
    ////////////////////////////////////////////////////////////////////////////////////
    /**
     * Update the specified resource in storage.
     */
    public function update(GameUpdateRequest $request, string $id)
    {
        $game = Game::find($id);

        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        } elseif ($game->status === 'I' || $game->status === 'E') {
            return response()->json(['error' => 'Game is not in progress'], 400);
        }

        $validated = $request->validated();

       
        if ($validated['status'] === 'E' && !array_key_exists('turns', $validated)) {
            return response()->json(['error' => 'When a game ends, total number of turns is required'], 400);
        }
        if ($validated['status'] === 'E' &&  is_null($validated['turns'])) {
            return response()->json(['error' => 'When a game ends, total number of turns is required'], 400);
        }

        

        if ($game->type == 'M') {
            $result = $this->updateMultiplayerGame($game, $validated, $request->user());
        } else {
            $result =  $this->updateSinglePlayerGame($game, $validated);
        }

        return $result instanceof JsonResponse ? $result : new GameResource($game);
    }

    private function updateMultiplayerGame($game, $validated, $user)
    {
        $game->status = $validated['status'];

        if ($validated['status'] === 'E') {
            if (is_null($validated['winner_user_id'])) {
                return response()->json(['error' => 'Game needs a winner to end'], 400);
            }

            $multi = MultiplayerGamesPlayed::where([['game_id', $game->id], ['user_id', $validated['user_id']]])->first();
            if (!$multi) {
                return response()->json(['error' => 'Player not found in the game'], 404);
            }

            $game->ended_at = now();
            $this->calculateGameTime($game);

            $transaction = new Transaction([
                'transaction_datetime' => now(),
                'user_id' => $user->id,
                'game_id' => $game->id,
                'type' => 'I',
            ]);

            if ($multi->user_id == $validated['winner_user_id']) {
                $game->total_turns_winner = $validated['turns'];
                $multi->player_won = 1;
                $user->brain_coins_balance += 7;
                $transaction->brain_coins = 7;
            } else {
                if ($user->brain_coins_balance < 5) {
                    return response()->json(['error' => 'Insufficient brain coins'], 400);
                }
                $multi->player_won = 0;
                $user->brain_coins_balance -= 5;
                $transaction->brain_coins = -5;
            }

            $user->save();
            $transaction->save();

            $multi->pairs_discovered = $validated['pairs_discovered'];
            $multi->save();
        }

        $game->save();
        return response()->json(['success' => 'Multiplayer game updated successfully']);
    }

    private function updateSinglePlayerGame($game, $validated)
    {
        if (array_key_exists('winner_user_id',$validated)) {
            return response()->json(['error' => "Single-player games don't have a winner"], 400);
        }

        $game->status = $validated['status'];

        if ($validated['status'] === 'E') {
            $game->ended_at = now();
            $game->total_turns_winner = $validated['turns'];
            $this->calculateGameTime($game);
        }

        $game->save();
        return response()->json(['success' => 'Single-player game updated successfully']);
    }


    private function calculateGameTime(Game $game)
    {
        $began_at = Carbon::parse($game->created_at);
        $ended_at = Carbon::parse($game->ended_at);
        $game->total_time = number_format($began_at->floatDiffInSeconds($ended_at), 2);
    }
    /////////////////////Update

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
