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
            ->whereDate('finished_at', '<', Carbon::now())
            ->get();

        return response()->json(['attackerShips' => $attackerShips], 200);
    }
}
