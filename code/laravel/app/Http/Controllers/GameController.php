<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\HistoryResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return GameResource::collection(Game::get());
    }
    /**
     * Display a listing of the resource without Unfinished Games.
     */
    public function indexFinished()
    {
        $games = Game::where('status', 'E')->with(['creator'])->get();
        return GameResource::collection($games);
    }

    public function indexHistory(Request $request)
    {
        $userId = $request->user()->id;

        // se o user for admin ele pode ver todos os jogos
        if ($request->user()->type == 'A') {
            $games = Game::with(['creator', 'winner', 'board', 'multiplayerGamesPlayed.user'])
                ->orderBy('began_at', 'desc')
                ->paginate(10);

            // se não ele só pode ver os jogos que ele criou ou participou
        } else {
            $games = Game::where('created_user_id', $userId)
                ->orWhereHas('multiplayerGamesPlayed', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with(['creator', 'winner', 'board', 'multiplayerGamesPlayed.user'])
                ->orderBy('began_at', 'desc')
                ->paginate(10);
        }

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

    public function indexScoreboardPersonal(Request $request)
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
            ->select('board_id', DB::raw('MIN(total_time) as total_time'), 'created_user_id')
            ->with(['board', 'creator'])
            ->groupBy('board_id', 'created_user_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_time' => $item->total_time,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                    'nickname' => $item->creator->nickname,
                ]];
            });

        $minTurns = Game::where('type', 'S')
            ->select('board_id', DB::raw('MIN(total_turns_winner) as total_turns'), 'created_user_id')
            ->with(['board', 'creator'])
            ->groupBy('board_id', 'created_user_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->board_id => [
                    'total_turns' => $item->total_turns,
                    'board_cols' => $item->board->board_cols,
                    'board_rows' => $item->board->board_rows,
                    'nickname' => $item->creator->nickname,
                ]];
            });

        $topPlayers = Game::where('type', 'M')
            ->select('winner_user_id', DB::raw('COUNT(*) as total_victories'), DB::raw('MAX(ended_at) as first_victory'))
            ->whereNotNull('winner_user_id')
            ->groupBy('winner_user_id')
            ->with('winner')
            ->orderByDesc('total_victories')
            ->orderBy('first_victory') 
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->winner_user_id,
                    'nickname' => $item->winner->nickname,
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
    public function store(GameRequest $request)
    {
        $game = Game::create($request->validated());

        return response()->json([
            'message' => 'Game created successfully!'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        return new GameResource($game);
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
