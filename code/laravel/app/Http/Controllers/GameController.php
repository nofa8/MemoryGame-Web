<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\HistoryResource;
use App\Models\Game;
use Illuminate\Http\Request;

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
