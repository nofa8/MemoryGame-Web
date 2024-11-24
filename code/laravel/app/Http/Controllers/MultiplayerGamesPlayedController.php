<?php

namespace App\Http\Controllers;

use App\Http\Resources\MultiplayerGamesPlayedResource;
use App\Models\MultiplayerGamesPlayed;
use Illuminate\Http\Request;

class MultiplayerGamesPlayedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MultiplayerGamesPlayedResource::collection(MultiplayerGamesPlayed::get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
