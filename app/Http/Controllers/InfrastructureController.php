<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfrastructureController extends Controller
{
    //
    public function buildMine(Request $request)
    {
        $resources = Resource::where('user_id', Auth::user()->id)->first();

        $availableOre = $resources->ore;
        $avalableEnergy = $resources->energy;

        // dd($availableOre);

        if ($availableOre >= 300 && $avalableEnergy >= 1) {
            $mine = new Infrastructure();
            $mine->user_id = Auth::user()->id;
            $mine->type = $request->type;
            $mine->level = 1;
            $mine->production_hour = 100;
            $mine->construction_cost = 300;
            $mine->finished_at = Carbon::now()->addHours(1);
            $mine->save();
            $newAvailableOre = $availableOre - $mine->construction_cost;
            $newAvailableEnergy = $avalableEnergy - 1;
            // dd($newAvailableOre);

            // update resource table
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $newAvailableEnergy]);

            return response()->json(['message' => 'Mine successfuly created', 'mine' => $mine], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }


    public function buildRefinery(Request $request)
    {
        $resources = Resource::where('user_id', Auth::user()->id)->first();

        $availableOre = $resources->ore;
        $avalableEnergy = $resources->energy;

        // dd($availableOre);

        if ($availableOre >= 300 && $avalableEnergy >= 1) {
            $refinery = new Infrastructure();
            $refinery->user_id = Auth::user()->id;
            $refinery->type = $request->type;
            $refinery->level = 1;
            $refinery->production_hour = 100;
            $refinery->construction_cost = 300;
            $refinery->finished_at = Carbon::now()->addHours(1);
            $refinery->save();

            $newAvailableOre = $availableOre - $refinery->construction_cost;
            $newAvailableEnergy = $avalableEnergy - 2;
            // dd($newAvailableOre);

            // update resource table
            Resource::where('user_id', Auth::user()->id)->update(['ore' => $newAvailableOre, 'energy' => $newAvailableEnergy]);

            return response()->json(['message' => 'Refinery successfuly created', 'refinery' => $refinery], 200);
        } else {
            return response()->json(['message' => 'You do not have enough resources'], 401);
        }
    }
}
