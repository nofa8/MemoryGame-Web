<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\User;
use App\Models\Transaction;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request)
    {
        $totalPlayers = User::where('type', 'P')->whereNull('deleted_at')->count();
        $totalGames = Game::count();
        $gamesLastWeek = Game::where('began_at', '>=', now()->subWeek())->count();
        $gamesLastMonth = Game::where('began_at', '>=', now()->subMonth())->count();
        $purchasesByTimePeriod = Transaction::selectRaw('DATE_FORMAT(transaction_datetime, "%Y-%m") as period, SUM(brain_coins) as total')
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        $purchasesByPlayer = User::where('users.type', 'P')
            ->whereNull('users.deleted_at')
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->selectRaw('users.nickname as player, SUM(transactions.brain_coins) as total')
            ->groupBy('users.id', 'users.nickname')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'totalPlayers' => $totalPlayers,
            'totalGames' => $totalGames,
            'gamesLastWeek' => $gamesLastWeek,
            'gamesLastMonth' => $gamesLastMonth,
            'purchasesByTimePeriod' => $purchasesByTimePeriod,
            'purchasesByPlayer' => $purchasesByPlayer,
        ]);
    }
}
