<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Requests\GameUpdateRequest;
use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\HistoryResource;
use App\Models\Game;
use App\Models\MultiplayerGamesPlayed;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isNull;

class GameController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  */
    public function indexTAES()
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




    public function indexHistory(Request $request)
    {
        $userId = $request->user()->id;

        if ($request->user()->type == 'A') {
            $query = Game::with(['creator' => function ($query) {
                $query->withTrashed();
            }, 'winner' => function ($query) {
                $query->withTrashed();
            }, 'board', 'multiplayerGamesPlayed.user' => function ($query) {
                $query->withTrashed();
            }]);
        } else {
            $query = Game::with(['creator' => function ($query) {
                $query->withTrashed();
            }, 'winner' => function ($query) {
                $query->withTrashed();
            }, 'board', 'multiplayerGamesPlayed.user' => function ($query) {
                $query->withTrashed();
            }])
                ->where(function ($query) use ($userId) {
                    $query->where('created_user_id', $userId)
                        ->orWhereHas('multiplayerGamesPlayed', function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        });
                });
        }

        if ($request->filled('board_size') && $request->input('board_size') !== 'All board sizes') {
            list($cols, $rows) = explode('x', $request->input('board_size'));
            $query->whereHas('board', function ($query) use ($cols, $rows) {
                $query->where('board_cols', $cols)
                    ->where('board_rows', $rows);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('began_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('began_at', '<=', $request->input('end_date'));
        }

        if ($request->filled('game_type') && $request->input('game_type') !== 'All types') {
            $gameType = $request->input('game_type') === 'Single Player' ? 'S' : 'M';
            $query->where('type', $gameType);
        }

        if ($request->user()->type == 'A' && $request->filled('creator')) {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('nickname', 'LIKE', '%' . $request->input('creator') . '%');
            });
        }

        $games = $query->orderBy('began_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => HistoryResource::collection($games),
            'meta' => [
                'current_page' => $games->currentPage(),
                'from' => $games->firstItem(),
                'last_page' => $games->lastPage(),
                'per_page' => $games->perPage(),
                'to' => $games->lastItem(),
                'total' => $games->total(),
            ]
        ]);
    }



    public function indexHistoryTAES(Request $request)
    {
        $userId = $request->user()->id;


        $games = Game::where('created_user_id', $userId)
            ->with(['creator'])
            ->orderBy('began_at', 'desc')
            ->take(10);

        return HistoryResource::collection($games);
    }

    public function indexScoreboardPersonal(Request $request)
    {
        $userId = $request->user()->id;

        $bestTimes = Game::where('created_user_id', $userId)
            ->where('type', 'S')
            ->where('total_time', '<>', null)
            ->with('board')
            ->select('board_id', 'total_time', 'board_id')
            ->with('board')
            ->get()
            ->groupBy(function ($item) {
                return $item->board->board_cols . 'x' . $item->board->board_rows;
            })
            ->map(function ($boardGroup) {
                return $boardGroup->sortBy('total_time')
                    ->take(3)
                    ->map(function ($game) {
                        return [
                            'total_time' => $game->total_time,
                            'board_cols' => $game->board->board_cols,
                            'board_rows' => $game->board->board_rows,
                        ];
                    })
                    ->values();
            });

        $minTurns = Game::where('created_user_id', $userId)
            ->where('type', 'S')
            ->where('total_turns_winner', '<>', null)
            ->with('board')
            ->select('board_id', 'total_turns_winner', 'board_id')
            ->with('board')
            ->get()
            ->groupBy(function ($item) {
                return $item->board->board_cols . 'x' . $item->board->board_rows;
            })
            ->map(function ($boardGroup) {
                return $boardGroup->sortBy('total_turns_winner')
                    ->take(3)
                    ->map(function ($game) {
                        return [
                            'total_turns_winner' => $game->total_turns_winner,
                            'board_cols' => $game->board->board_cols,
                            'board_rows' => $game->board->board_rows,
                        ];
                    })
                    ->values();
            });

        $multiplayerStats = DB::table('multiplayer_games_played')
            ->where('user_id', $userId)
            ->selectRaw('SUM(CASE WHEN player_won = 1 THEN 1 ELSE 0 END) as total_victories')
            ->selectRaw('SUM(CASE WHEN player_won = 0 THEN 1 ELSE 0 END) as total_losses')
            ->first();

        return response()->json([
            'best_times' => $bestTimes,
            'min_turns' => $minTurns,
            'multiplayer_stats' => $multiplayerStats
        ]);
    }

    public function indexScoreboardPersonalTAES(Request $request)
    {
        $userId = $request->user()->id;

        // melhor tempo para cada board em jogos singleplayer de um user
        $bestTimes = Game::where('created_user_id', $userId)
            ->where('type', 'S')
            ->with('board')
            ->select('board_id', DB::raw('MIN(total_time) as total_time'))
            ->groupBy('board_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_time' => $item->total_time,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                ]];
            });

        // menor numero de jogadas para cada board em jogos singleplayer de um user
        $minTurns = Game::where('created_user_id', $userId)
            ->where('type', 'S')
            ->with('board')
            ->select('board_id', DB::raw('MIN(total_turns_winner) as total_turns_winner'))
            ->groupBy('board_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_turns_winner' => $item->total_turns_winner,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                ]];
            });

        // vitórias e derrotas em jogos multiplayer de um user
        $multiplayerStats = DB::table('multiplayer_games_played')
            ->where('user_id', $userId)
            ->selectRaw('SUM(CASE WHEN player_won = 1 THEN 1 ELSE 0 END) as total_victories')
            ->selectRaw('SUM(CASE WHEN player_won = 0 THEN 1 ELSE 0 END) as total_losses')
            ->first();

        return response()->json([
            'best_times' => $bestTimes,
            'min_turns' => $minTurns,
            'multiplayer_stats' => $multiplayerStats
        ]);
    }

    public function indexScoreboardGlobal()
    {
        $bestTimes = Game::where('type', 'S')
            ->where('total_time', '<>', null)
            ->select('board_id', DB::raw('MIN(total_time) as total_time'), 'created_user_id')
            ->with(['board:id,board_cols,board_rows', 'creator:id,nickname'])
            ->whereHas('creator', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->groupBy('board_id', 'created_user_id')
            ->orderBy('total_time', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_time' => $item->total_time,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                    'nickname' => $item->creator?->nickname,
                ]];
            });

        $minTurns = Game::where([['type', 'S'], ['total_turns_winner', '<>', null]])
            ->select('board_id', DB::raw('MIN(total_turns_winner) as total_turns'), 'created_user_id')
            ->with(['board', 'creator'])
            ->whereHas('creator', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->groupBy('board_id', 'created_user_id')
            ->orderBy('total_turns', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_turns' => $item->total_turns,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                    'nickname' => $item->creator?->nickname,
                ]];
            });

        $topPlayers = Game::where('type', 'M')
            ->select('winner_user_id', DB::raw('COUNT(*) as total_victories'), DB::raw('MAX(ended_at) as first_victory'))
            ->whereNotNull('winner_user_id')
            ->whereHas('winner', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->groupBy('winner_user_id')
            ->with('winner')
            ->orderByDesc('total_victories')
            ->orderBy('first_victory')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->winner_user_id,
                    'nickname' => $item?->winner?->nickname,
                    'total_victories' => $item->total_victories
                ];
            });

        return response()->json([
            'best_times' => $bestTimes,
            'min_turns' => $minTurns,
            'top_players' => $topPlayers,
        ]);
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
        if ($game->type === 'M') {
            return response()->json(['error' => 'Not a singleplayer game'], 400);
        }
        $validated = $request->validated();


        if ($validated['status'] === 'E' && !array_key_exists('turns', $validated)) {
            return response()->json(['error' => 'When a game ends, total number of turns is required'], 400);
        }
        if ($validated['status'] === 'E' &&  is_null($validated['turns'])) {
            return response()->json(['error' => 'When a game ends, total number of turns is required'], 400);
        }

        $result =  $this->updateSinglePlayerGame($game, $validated);


        return $result instanceof JsonResponse ? $result : new GameResource($game);
    }



    private function updateSinglePlayerGame($game, $validated)
    {
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
