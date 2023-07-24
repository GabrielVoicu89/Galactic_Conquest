<?php

namespace App\Http\Controllers;

use App\Models\Ship;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    public function attack($defenderId)
    {
        $attackerShips = Ship::where('user_id', Auth::user()->id)
            ->where('finished_at', '<', Carbon::now())
            ->where('claimed', true)
            ->get();
        $defenderShips = Ship::where('user_id', $defenderId)
            ->where('finished_at', '<', Carbon::now())
            ->where('claimed', true)
            ->get();

        return response()->json([
            'attackerShips' => $attackerShips,
            'defenderShips' => $defenderShips
        ], 200);
    }
}
